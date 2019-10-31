<?php

namespace App\Entity;

use App\Traits\TimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Player
{
    use TimeTrait;

    const TYPE_PROFESSIONAL = 'PROFESSIONAL';
    const TYPE_JUNIOR = 'JUNIOR';

    const POSITION_GOALKEEPER = 'GOALKEEPER';
    const POSITION_DEFENDER = 'DEFENDER';
    const POSITION_MIDFIELD = 'MIDFIELD';
    const POSITION_FORWARD = 'FORWARD';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"player", "clubPlayer"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"player", "clubPlayer"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Email
     * @Groups({"player", "clubPlayer"})
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"player", "clubPlayer"})
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice(callback="getPositions")
     * @Groups({"player", "clubPlayer"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice(callback="getTypes")
     * @Groups({"player", "clubPlayer"})
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"player", "clubPlayer"})
     */
    private $salary = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="players")
     * @Groups({"player"})
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

    /* START DONT REMOVE */
    public static function getPositions()
    {
        return [self::POSITION_GOALKEEPER, self::POSITION_DEFENDER, self::POSITION_MIDFIELD, self::POSITION_FORWARD];
    }

    public static function getTypes()
    {
        return [self::TYPE_PROFESSIONAL, self::TYPE_JUNIOR];
    }
    /* FINISH DONT REMOVE */

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSalary(): ?float
    {
        return (float)$this->salary;
    }

    public function setSalary(float $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }
}