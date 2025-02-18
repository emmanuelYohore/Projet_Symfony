<?php

namespace App\Entity;

use App\Repository\HabitsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HabitsRepository::class)]
class Habits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer')]
    private ?int $difficulty = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $periodicity = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'habits')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $creator = null;

    #[ORM\ManyToOne(targetEntity: Groups::class, inversedBy: 'habits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Groups $group = null;

    // Getters et Setters

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getPeriodicity(): ?string
    {
        return $this->periodicity;
    }

    public function setPeriodicity(string $periodicity): self
    {
        $this->periodicity = $periodicity;
        return $this;
    }

    public function getCreator(): ?Users
    {
        return $this->creator;
    }

    public function setCreator(?Users $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    public function getGroup(): ?Groups
    {
        return $this->group;
    }

    public function setGroup(?Groups $group): self
    {
        $this->group = $group;
        return $this;
    }
}
