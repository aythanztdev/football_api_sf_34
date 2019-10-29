<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Coach;
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

    /**
     * @param $player
     */
    public function delete($player)
    {
        $this->remove($player);
    }

    /**
     * @return Player[]
     */
    public function getAll()
    {
        return $this->playerRepository->findAll();
    }
}