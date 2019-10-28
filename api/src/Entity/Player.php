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

    const TYPE_PLAYER = 'PROFESSIONAL';
    const TYPE_JUNIOR = 'JUNIOR';

    const POSITION_GOALKEEPER = 'GOALKEEPER';
    const POSITION_DEFENDER = 'DEFENDER';
    const POSITION_MIDFIELD = 'MIDFIELD';
    const POSITION_FORWARD = 'FORWARD';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"member"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"member"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"member"})
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"member"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"member"})
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Groups({"member"})
     */
    private $salary;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="players")
     */
    private $club;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"member"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"member"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"member"})
     */
    private $deletedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getSalary()
    {
        return (float)$this->salary;
    }

    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }
}