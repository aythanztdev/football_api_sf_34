<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    protected $entityManager;

    /**
     * AbstractService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save()
    {
        $this->entityManager->flush();
    }
}