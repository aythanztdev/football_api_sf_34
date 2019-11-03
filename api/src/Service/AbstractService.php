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

    /**
     * @param mixed $object
     */
    public function saveThisObjectOnly($object)
    {
        $this->entityManager->flush($object);
    }

    /**
     * @param mixed $object
     */
    public function remove($object)
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }
}
