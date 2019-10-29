<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param bool $isPatchOrPut
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function customValidations(Player $player, $isPatchOrPut = false)
    {
        $errors = [];

        $club = $player->getClub();
        if (!$club instanceof Club) {
            return $errors;
        }

        $totalSalaries = $this->playerRepository->getTotalSalaries($club) + $player->getSalary();
        $clubBudget = $club->getBudget();
        if ($totalSalaries > $clubBudget) {
            $errors[] = sprintf('Total salaries can be greater than %s', $clubBudget);
        }

        if ($player->getType() === Player::TYPE_PROFESSIONAL) {
            $errorsProfessionalPlayer = $this->validateProfessionalPlayer($player, $isPatchOrPut);
            $errors = array_merge($errors, $errorsProfessionalPlayer);
        }

        return $errors;
    }

    /**
     * @param Player $player
     * @param $isPatchOrPut
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function validateProfessionalPlayer(Player $player, $isPatchOrPut)
    {
        $errors = [];
        if (empty($player->getSalary())) {
            $errors[] = 'This value should not be blank or 0';
        }

        if ($isPatchOrPut) {
            return $errors;
        }

        $totalProfessionalPlayers = $this->playerRepository->getTotalPlayers($player->getClub(), Player::TYPE_PROFESSIONAL) + 1;
        if ($totalProfessionalPlayers > Club::MAX_LIMIT_PLAYERS) {
            $errors[] = sprintf('Total players per team cannot be greater than %s', Club::MAX_LIMIT_PLAYERS);
        }

        return $errors;
    }
}