<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\\Repository\\UserRepository")]
#[ORM\Table(name: "users")]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $lastConnection = null;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private int $points = 0;

    #[ORM\ManyToOne(targetEntity: "App\\Entity\\Groups")]
    #[ORM\JoinColumn(name: "group_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?Groups $group = null;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }
    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getProfilePicture(): ?string { return $this->profilePicture; }
    public function setProfilePicture(?string $profilePicture): self { $this->profilePicture = $profilePicture; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getLastConnection(): ?\DateTimeInterface { return $this->lastConnection; }
    public function setLastConnection(?\DateTimeInterface $lastConnection): self { $this->lastConnection = $lastConnection; return $this; }
    public function getPoints(): int { return $this->points; }
    public function setPoints(int $points): self { $this->points = $points; return $this; }
    public function getGroup(): ?Groups { return $this->group; }
    public function setGroup(?Groups $group): self { $this->group = $group; return $this; }
} 