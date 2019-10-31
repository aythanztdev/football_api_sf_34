<?php


namespace App\Entity;

use App\Traits\TimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="asset")
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Asset
{
    use TimeTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"asset", "club"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"asset", "club"})
     */
    private $path;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Club", mappedBy="shield")
     */
    private $club;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        // set (or unset) the owning side of the relation if necessary
        $newShield = null === $club ? null : $this;
        if ($club->getShield() !== $newShield) {
            $club->setShield($newShield);
        }

        return $this;
    }
}