<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Repository\UserHabitRepository;

#[ODM\Document(repositoryClass: UserHabitRepository::class)]
class UserHabit
{
    #[ODM\Id(strategy: 'NONE')]
    private ?string $id = null;

    #[ODM\ReferenceOne(targetDocument: User::class, storeAs: 'id')]
    private string $user_id;

    #[ODM\ReferenceOne(targetDocument: Habit::class, storeAs: 'id')]
    private string $habit_id;

    // Getters and setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getHabitId(): string
    {
        return $this->habit_id;
    }

    public function setHabitId(string $habit_id): self
    {
        $this->habit_id = $habit_id;

        return $this;
    }
}