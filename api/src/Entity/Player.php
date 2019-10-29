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
     * @Groups({"player", "club"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"player", "club"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"player", "club"})
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Choice(callback="getPositions")
     * @Groups({"player", "club"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"player", "club"})
     */
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank
     * @Groups({"player", "club"})
     */
    private $salary;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="players")
     * @Assert\NotBlank
     * @Groups({"player"})
     */
    private $club;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"player", "club"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"player", "club"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"player"})
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

    public function getClub()
    {
        return $this->club;
    }

    public function setClub($club)
    {
        $this->club = $club;
    }
}