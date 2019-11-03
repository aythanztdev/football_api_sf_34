<?php

namespace App\Service;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class AssetService extends AbstractService
{
    /**
     * AssetService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * @param Asset $asset
     */
    public function persistAndSave(Asset $asset)
    {
        $this->entityManager->persist($asset);
        $this->save();
    }

    public function setPath(Asset $asset, string $host, string $assetPath)
    {
        $fullPath = sprintf('%s%s', $host, $assetPath);
        $asset->setPath($fullPath);
    }
}
