<?php

namespace App\Entity;

use App\Repository\PointsLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointsLogRepository::class)]
class PointsLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupe $group_id = null;

    #[ORM\Column]
    private ?int $points_change = null;

    #[ORM\Column(length: 255)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getGroupId(): ?Groupe
    {
        return $this->group_id;
    }

    public function setGroupId(?Groupe $group_id): static
    {
        $this->group_id = $group_id;

        return $this;
    }

    public function getPointsChange(): ?int
    {
        return $this->points_change;
    }

    public function setPointsChange(int $points_change): static
    {
        $this->points_change = $points_change;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
