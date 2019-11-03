<?php


namespace App\Service;

use App\Entity\Club;
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
    ) {
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

    /**
     * @param Coach $coach
     */
    public function unsetLastCoachOnClub(Coach $coach)
    {
        $club = $coach->getClub();
        if (!$club instanceof Club) {
            return;
        }

        $lastCoach = $this->coachRepository->findOneBy(['club' => $club]);
        if ($lastCoach instanceof Coach && $lastCoach !== $coach) {
            $lastCoach->setClub(null);
            $this->saveThisObjectOnly($lastCoach);
        }
    }
}
