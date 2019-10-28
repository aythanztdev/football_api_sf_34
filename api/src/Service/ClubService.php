<?php

namespace App\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubService
{
    private $entityManager;
    private $clubRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClubRepository $clubRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->clubRepository = $clubRepository;
    }

    public function save(Club $club)
    {
        $this->entityManager->persist($club);
        $this->entityManager->flush();
    }

    public function getAll()
    {
        return $this->clubRepository->findAll();
    }
}