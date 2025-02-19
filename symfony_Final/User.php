<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[MongoDB\Document(collection: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $firstName;

    #[MongoDB\Field(type: "string")]
    private $lastName;

    #[MongoDB\Field(type: "string", unique: true)]
    private $username;

    #[MongoDB\Field(type: "string")]
    private $password;

    #[MongoDB\Field(type: "string", nullable: true)]
    private $profilePicture;

    public function getId(): ?string { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self { $this->username = $username; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getProfilePicture(): ?string { return $this->profilePicture; }
    public function setProfilePicture(?string $profilePicture): self { $this->profilePicture = $profilePicture; return $this; }

    public function getRoles(): array { return ['ROLE_USER']; }
    public function eraseCredentials(): void {}
}
