<?php

namespace App\Entity;

use App\Traits\TimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="coach")
 * @ORM\Entity(repositoryClass="App\Repository\CoachRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Coach
{
    use TimeTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"coach"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"coach", "club"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Email
     * @Groups({"coach", "club"})
     */
    private $email;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank
     * @Groups({"coach", "club"})
     */
    private $salary;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Club", inversedBy="coach")
     * @Assert\NotBlank
     * @Groups({"coach"})
     */
    private $club;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"coach"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"coach"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"coach"})
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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

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

    public function setClub(Club $club)
    {
        $this->club = $club;

        return $this;
    }
}