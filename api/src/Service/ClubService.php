<?php

namespace App\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubService extends AbstractService
{
    private $clubRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClubRepository $playerRepository
    )
    {
        $this->clubRepository = $playerRepository;
        parent::__construct($entityManager);
    }

    public function persistAndSave(Club $club)
    {
        $this->entityManager->persist($club);
        $this->save();
    }

    public function getAll()
    {
        return $this->clubRepository->findAll();
    }
}