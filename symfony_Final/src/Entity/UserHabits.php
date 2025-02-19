<?php

namespace App\Entity;

use App\Repository\UserHabitsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserHabitsRepository::class)]
#[ORM\Table(name: 'user_habits')]
class UserHabits
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'userHabits')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Habits::class, inversedBy: 'userHabits')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Habits $habit = null;

    // Getters et Setters

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getHabit(): ?Habits
    {
        return $this->habit;
    }

    public function setHabit(?Habits $habit): self
    {
        $this->habit = $habit;
        return $this;
    }
}
