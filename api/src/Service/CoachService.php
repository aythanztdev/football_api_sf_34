<?php


namespace App\Service;


use App\Entity\Coach;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;

class CoachService extends AbstractService
{
    private $coachRepository;

    /**
     * CoachService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CoachRepository $coachRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CoachRepository $coachRepository
    )
    {
        $this->coachRepository = $coachRepository;
        parent::__construct($entityManager);
    }

    /**
     * @param Coach $coach
     */
    public function persistAndSave(Coach $coach)
    {
        $this->entityManager->persist($coach);
        $this->save();
    }

    /**
     * @param $coach
     */
    public function delete($coach)
    {
        $this->remove($coach);
    }

    /**
     * @return Coach[]
     */
    public function getAll()
    {
        return $this->coachRepository->findAll();
    }
}