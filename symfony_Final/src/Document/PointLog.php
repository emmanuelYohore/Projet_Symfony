<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

<<<<<<< HEAD
#[ODM\Document(collection: 'points_log')]
=======
/**
 * @ODM\Document(repositoryClass=PointsLogRepository::class)
 */
>>>>>>> origin/emmanuel
class PointLog
{
    #[ODM\Id(strategy: 'AUTO')]
    private ?string $id = null;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    private ?User $user = null;

    #[ODM\ReferenceOne(targetDocument: Group::class, nullable: true)]
    private ?Group $group = null;

    #[ODM\ReferenceOne(targetDocument: Habit::class, nullable: true)]
    private ?Habit $habit = null;

    #[ODM\Field(type: 'int')]
    private int $pointsChange;

<<<<<<< HEAD
    #[ODM\Field(type: 'string')]
    private string $reason;

    #[ODM\Field(type: 'date')]
    private \DateTime $timestamp;

    public function __construct()
    {
        $this->timestamp = new \DateTime();
    }

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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getHabit(): ?Habit
    {
        return $this->habit;
    }

    public function setHabit(?Habit $habit): self
    {
        $this->habit = $habit;
        return $this;
    }

    public function getPointsChange(): int
    {
        return $this->pointsChange;
    }

    public function setPointsChange(int $pointsChange): self
    {
        $this->pointsChange = $pointsChange;
        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
=======
    /**
     * @ODM\Field(type="date")
     */
    private $timestamp;
>>>>>>> origin/emmanuel
}