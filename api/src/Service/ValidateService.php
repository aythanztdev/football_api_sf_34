<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Coach;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\NonUniqueResultException;

class ValidateService
{
    const MAX_AGE_JUNIOR = 23;

    private $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    /**
     * @param Player $player
     * @param $lastClub
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function playerValidation(Player $player, $lastClub = null)
    {
        $errors = [];

        if ($player->getType() === Player::TYPE_JUNIOR) {
            $errorsJuniorAge = $this->validateAge($player);
            $errors = array_merge($errors, $errorsJuniorAge);
        }

        if ($player->getClub() instanceof Club) {
            $errorsPlayersClub = $this->validatePlayerClubConditions($player, $lastClub);
            $errors = array_merge($errors, $errorsPlayersClub);
        }

        return $errors;
    }

    /**
     * @param Coach $coach
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function coachValidation(Coach $coach)
    {
        $errors = [];

        $club = $coach->getClub();
        if ($club instanceof Club) {
            $club->setCoach($coach);
            $errorsPlayersClub = $this->validateCoachClubConditions($coach);
            $errors = array_merge($errors, $errorsPlayersClub);
        }

        return $errors;
    }

    /**
     * @param Player $player
     * @param $lastClub
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    private function validatePlayerClubConditions(Player $player, $lastClub)
    {
        $errors = [];

        if ($player->getType() === Player::TYPE_PROFESSIONAL && empty($player->getSalary())) {
            $errors[] = 'This value should not be blank or 0';
        }

        $errorsSalaries = $this->validateSalaries($player->getClub(), $player->getSalary());
        $errors = array_merge($errors, $errorsSalaries);

        $totalProfessionalPlayers = $this->playerRepository->getTotalPlayers($player->getClub(), Player::TYPE_PROFESSIONAL);
        if ($totalProfessionalPlayers >= Club::MAX_LIMIT_PLAYERS && $lastClub !== $player->getClub()) {
            $errors[] = sprintf('Total players per team cannot be greater than %s', Club::MAX_LIMIT_PLAYERS);
        }

        return $errors;
    }

    /**
     * @param Coach $coach
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function validateCoachClubConditions(Coach $coach)
    {
        $errors = $this->validateSalaries($coach->getClub());

        return $errors;
    }

    /**
     * @param Club $club
     * @param float $playerSalary
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    private function validateSalaries(Club $club, float $playerSalary = 0)
    {
        $errors = [];

        $coachSalary = 0;
        $coach = $club->getCoach();
        if ($coach instanceof Coach) {
            $coachSalary = $coach->getSalary();
        }

        $totalSalaries = $this->playerRepository->getTotalSalaries($club) + $playerSalary + $coachSalary;
        $clubBudget = $club->getBudget();
        if ($totalSalaries > $clubBudget) {
            $errors[] = sprintf('Total salaries cannot be greater than %s', $clubBudget);
        }

        return $errors;
    }

    private function validateAge(Player $player)
    {
        $errors = [];

        $dateNow = new \DateTime();
        $dateNow->setTime(0, 0, 0);

        $datePlayer = $player->getBirthday()->setTime(0, 0, 0);
        $datePlayer->add(new \DateInterval(sprintf('P%sY', self::MAX_AGE_JUNIOR)));

        if ($dateNow > $datePlayer) {
            $errors[] = sprintf('Junior player must not be more than %s years old', self::MAX_AGE_JUNIOR);
        }

        return $errors;
    }
}