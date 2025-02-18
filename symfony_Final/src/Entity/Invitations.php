<?php

namespace App\Entity;

use App\Repository\InvitationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationsRepository::class)]
class Invitations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Users $receiver = null;

    #[ORM\ManyToOne(targetEntity: Groups::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Groups $group = null;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private ?string $status = 'pending';

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getReceiver(): ?Users
    {
        return $this->receiver;
    }

    public function setReceiver(?Users $receiver): self
    {
        $this->receiver = $receiver;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, ['pending', 'accepted', 'declined'])) {
            throw new \InvalidArgumentException("Statut invalide.");
        }
        $this->status = $status;
        return $this;
    }
}
