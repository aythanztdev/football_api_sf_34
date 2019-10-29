<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class PlayerService extends AbstractService
{
    private $playerRepository;

    /**
     * PlayerService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PlayerRepository $playerRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository
    )
    {
        $this->playerRepository = $playerRepository;
        parent::__construct($entityManager);
    }

    /**
     * @param Player $player
     */
    public function persistAndSave(Player $player)
    {
        $this->entityManager->persist($player);
        $this->save();
    }

    public function getAll()
    {
        return $this->playerRepository->findAll();
    }

    public function delete(Player $player)
    {
        $this->entityManager->remove($player);
        $this->save();
    }

    /**
     * @param Player $player
     * @param $lastClub
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function customValidations(Player $player, $lastClub = null)
    {
        $errors = [];

        if ($player->getType() === Player::TYPE_JUNIOR) {
            $errorsJuniorAge = $this->validateAge($player);
            $errors = array_merge($errors, $errorsJuniorAge);
        }

        $errorsPlayersClub = $this->validatePlayersClubConditions($player, $lastClub);
        $errors = array_merge($errors, $errorsPlayersClub);

        return $errors;
    }

    private function validateAge(Player $player)
    {
        $maxYears = 23;
        $errors = [];

        $dateNow = new \DateTime();
        $dateNow->setTime(0, 0, 0);

        $datePlayer = $player->getBirthday()->setTime(0, 0, 0);
        $datePlayer->add(new \DateInterval(sprintf('P%sY', $maxYears)));

        if ($dateNow > $datePlayer) {
            $errors[] = sprintf('Junior player must not be more than %s years old', $maxYears);
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
    private function validatePlayersClubConditions(Player $player, $lastClub)
    {
        $errors = [];
        $club = $player->getClub();
        if (!$club instanceof Club) {
            return $errors;
        }

        if ($player->getType() === Player::TYPE_PROFESSIONAL && empty($player->getSalary())) {
            $errors[] = 'This value should not be blank or 0';
        }

        $totalSalaries = $this->playerRepository->getTotalSalaries($club) + $player->getSalary();
        $clubBudget = $club->getBudget();
        if ($totalSalaries > $clubBudget) {
            $errors[] = sprintf('Total salaries cannot be greater than %s', $clubBudget);
        }

        $totalProfessionalPlayers = $this->playerRepository->getTotalPlayers($player->getClub(), Player::TYPE_PROFESSIONAL);
        if ($totalProfessionalPlayers >= Club::MAX_LIMIT_PLAYERS && $lastClub !== $player->getClub()) {
            $errors[] = sprintf('Total players per team cannot be greater than %s', Club::MAX_LIMIT_PLAYERS);
        }

        return $errors;
    }
}