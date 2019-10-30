<?php

namespace App\Entity;

use App\Traits\TimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="club")
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Club
{
    use TimeTrait;

    const MAX_LIMIT_PLAYERS = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"club", "player", "coach"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"club", "player", "coach"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"club"})
     */
    private $shieldFileName;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"club"})
     */
    private $budget;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="club")
     * @Groups({"club"})
     */
    private $players;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Coach", inversedBy="club")
     * @Groups({"club"})
     */
    private $coach;

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

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShieldFileName(): ?string
    {
        return $this->shieldFileName;
    }

    public function setShieldFileName(string $shieldFileName): self
    {
        $this->shieldFileName =  $shieldFileName;

        return $this;
    }

    public function getBudget(): ?float
    {
        return (float)$this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setClub($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getClub() === $this) {
                $player->setClub(null);
            }
        }

        return $this;
    }

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;

        // set (or unset) the owning side of the relation if necessary
        $newClub = null === $coach ? null : $this;
        if ($coach->getClub() !== $newClub) {
            $coach->setClub($newClub);
        }

        return $this;
    }
}