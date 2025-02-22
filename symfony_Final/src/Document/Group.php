<?php
declare(strict_types=1);
// src/Document/Group.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'groups')]

class Group
{
    
    #[ODM\Id(strategy: 'AUTO')]
    private $id;

    #[ODM\Field(type: 'string')]
    private $name;

    #[ODM\Field(type: "int")]
    private $totalPoints;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    private ?User $creator = null;

    #[ODM\field(type: "bool")]
    private $created_habit_today = false;

    public function __construct()
    {
        $this->totalPoints = 0;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ?self
    {
        if ($name)
        {
            $this->name = $name;
        }
        return $this;
    }

    public function getPoints(): ?int{
        return $this->totalPoints;
    }
    public function setPoints(int $points): self{
        $this->totalPoints = $points;
        return $this;
    }

    public function getCreator(): ?User {
        return $this->creator;
    }

    public function setCreator(User $user): self
    {
        $this->creator = $user;
        return $this;
    }

    public function getCreatedHabitToday(): bool
    {
        return $this->created_habit_today;
    }

    public function setCreatedHabitToday(bool $created_habit_today): self
    {
        $this->created_habit_today = $created_habit_today;
        return $this;
    }
}
