<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayerService extends AbstractService
{
    private $playerRepository;
    private $mailerService;

    /**
     * PlayerService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PlayerRepository $playerRepository
     * @param MailerService $mailerService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository,
        MailerService $mailerService
    ) {
        $this->playerRepository = $playerRepository;
        $this->mailerService = $mailerService;
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
