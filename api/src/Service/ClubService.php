<?php

namespace App\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClubService extends AbstractService
{
    private $clubRepository;

    /**
     * ClubService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ClubRepository $playerRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ClubRepository $playerRepository
    ) {
        $this->clubRepository = $playerRepository;
        parent::__construct($entityManager);
    }

    /**
     * @param Club $club
     */
    public function persistAndSave(Club $club)
    {
        $this->entityManager->persist($club);
        $this->save();
    }

    /**
     * @return Club[]
     */
    public function getAll()
    {
        return $this->clubRepository->findAll();
    }
}
