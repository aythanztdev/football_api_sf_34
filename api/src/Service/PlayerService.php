<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayerService extends AbstractService
{
    private $playerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository
    )
    {
        $this->playerRepository = $playerRepository;
        parent::__construct($entityManager);
    }

    public function persistAndSave(Player $player)
    {
        $this->entityManager->persist($player);
        $this->save();
    }

    public function getAll()
    {
        return $this->playerRepository->findAll();
    }
}