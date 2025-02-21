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

    #[ODM\Field(type: 'bool', nullable: true)]
    public ?bool $completed = null;
    
    #[ODM\Field(type: 'date')]
    public ?\DateTime $completedAt = null;

    public function __construct()
    {
        $this->completed = true;
        $this->completedAt = new \DateTime();
    }

    // Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): User
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

    public function getCompleted():bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $comp) :self
    {
        $this->completed = $comp;
        return $this;
    }


    public function getCompletedAt(): \DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}