<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Coach;
use App\Entity\Player;
use App\Exception\ClubException;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\NonUniqueResultException;

class ValidateService
{
    const MAX_AGE_JUNIOR = 23;

    private $playerRepository;
    private $clubRepository;

    /**
     * ValidateService constructor.
     *
     * @param PlayerRepository $playerRepository
     * @param ClubRepository $clubRepository
     */
    public function __construct(
        PlayerRepository $playerRepository,
        ClubRepository $clubRepository
    ) {
        $this->playerRepository = $playerRepository;
        $this->clubRepository = $clubRepository;
    }

    /**
     * @param Player $player
     * @param mixed $lastClub
     * @return array
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function playerValidation(Player $player, $lastClub = null)
    {
        $errors = [];

        if ($player->getType() === Player::TYPE_JUNIOR) {
            $errorsJuniorAge = $this->validateAge($player);
            $errors = array_merge($errors, $errorsJuniorAge);
        }

        $playerSalary = $player->getSalary();
        if ($player->getType() === Player::TYPE_PROFESSIONAL && empty($playerSalary)) {
            $errors[] = 'Salary must not be blank or 0';
        }

        if ($player->getType() === Player::TYPE_JUNIOR && !empty($playerSalary)) {
            $errors[] = 'Salary must be blank or 0 for junior players';
        }

        $errorsPlayersClub = $this->validatePlayerClubConditions($player, $lastClub);
        $errors = array_merge($errors, $errorsPlayersClub);

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

    public function clubValidation(Club $club)
    {
        $errors = [];

        $clubWithThisShield =  $this->clubRepository->findOneBy(['shield' => $club->getShield()]);
        if ($clubWithThisShield instanceof Club && $clubWithThisShield !== $club) {
            $errors[] = 'This shield belong to other club.';
        }

        return $errors;
    }

    /**
     * @param Player $player
     * @param mixed $lastClub
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    private function validatePlayerClubConditions(Player $player, $lastClub)
    {
        $errors = [];

        if (!$player->getClub() instanceof Club) {
            return $errors;
        }

        $playerSalary = $player->getSalary();
        if (empty($playerSalary)) {
            $playerSalary = 0;
        }

        $errorsSalaries = $this->validateSalaries($player->getClub(), $playerSalary);
        $errors = array_merge($errors, $errorsSalaries);

        if ($player->getType() === Player::TYPE_PROFESSIONAL) {
            $errorsMaxPlayers = $this->validateMaxPlayersPerClub($player, $lastClub);
            $errors = array_merge($errors, $errorsMaxPlayers);
        }

        return $errors;
    }

    /**
     * @param Coach $coach
     * @return array
     *
     * @throws NonUniqueResultException
     */
    private function validateCoachClubConditions(Coach $coach)
    {
        $errors = [];

        if (!$coach->getClub() instanceof Club) {
            return $errors;
        }

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
    public function validateSalaries(Club $club, float $playerSalary = 0.0)
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

    /**
     * @param Player $player
     *
     * @return array
     *
     * @throws \Exception
     */
    private function validateAge(Player $player)
    {
        $errors = [];

        $dateNow = new \DateTime();
        $dateNow->setTime(0, 0, 0);

        $datePlayer = new \DateTime();
        $datePlayer->setTimestamp($player->getBirthday()->getTimestamp());

        $datePlayer = $datePlayer->setTime(0, 0, 0);
        $datePlayer->add(new \DateInterval(sprintf('P%sY', self::MAX_AGE_JUNIOR)));

        if ($dateNow > $datePlayer) {
            $errors[] = sprintf('Junior player must not be more than %s years old', self::MAX_AGE_JUNIOR);
        }

        return $errors;
    }

    /**
     * @param Player $player
     * @param mixed $lastClub
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    private function validateMaxPlayersPerClub(Player $player, $lastClub)
    {
        $errors = [];
        $totalProfessionalPlayers = $this->playerRepository->getTotalPlayers($player->getClub(), Player::TYPE_PROFESSIONAL);
        if ($totalProfessionalPlayers >= Club::MAX_LIMIT_PLAYERS && $lastClub !== $player->getClub()) {
            $errors[] = sprintf('Total players per team cannot be greater than %s', Club::MAX_LIMIT_PLAYERS);
        }

        return $errors;
    }
}
