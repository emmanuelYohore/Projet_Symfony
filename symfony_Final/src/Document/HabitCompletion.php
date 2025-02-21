<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'habit_completion')]
class HabitCompletion
{
    #[ODM\Id(strategy: 'AUTO')]
    public ?string $id = null;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    public ?User $user = null;

    #[ODM\ReferenceOne(targetDocument: Habit::class)]
    public ?Habit $habit = null;

    #[ODM\Field(type: 'bool')]
    public bool $completed = false;

    #[ODM\Field(type: 'date')]
    public ?\DateTime $completed_at = null ; 

    #[ODM\Field(type: 'date')]
    public ?\DateTime $start_date = null ; 

    #[ODM\Field(type: 'date')]
    public ?\DateTime $end_date = null ; 

    public function __construct()
    {
        // Initialisation des valeurs par défaut si nécessaire
    }

    // Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHabit(): ?Habit
    {
        return $this->habit;
    }

    public function setHabit(Habit $habit): self
    {
        $this->habit = $habit;

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completed_at;
    }

    public function setCompletedAt(?\DateTime $completed_at): self
    {
        $this->completed_at = $completed_at;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTime $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTime $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }
}