<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'users')]
#[ODM\Index(keys: ['username' => 'asc'], options: ['unique' => true])]
#[ODM\Index(keys: ['email' => 'asc'], options: ['unique' => true])]
class User
{
    #[ODM\Id(strategy: 'AUTO')]
    public ?string $id = null;

    #[ODM\Field(type: 'string')]
    public string $first_name;

    #[ODM\Field(type: 'string')]
    public string $last_name;

    #[ODM\Field(type: 'string')]
    public string $username;

    #[ODM\Field(type: 'string')]
    public string $email;

    #[ODM\Field(type: 'string')]
    public string $password;

    #[ODM\Field(type: 'string', nullable: true)]
    public ?string $profile_picture = null;

    #[ODM\Field(type: 'date')]
    public \DateTime $created_at;

    #[ODM\Field(type: 'date')]
    public \DateTime $last_connection;

    #[ODM\Field(type: 'int')]
    public int $points;

    #[ODM\ReferenceOne(targetDocument: Group::class)]
    public ?Group $group = null;

    #[ODM\Field(type: "collection")]
    public array $habit_ids = [];

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->last_connection = new \DateTime();
        $this->points = 0;
        $this->habit_ids = [];
    }

    // Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getLastConnection(): ?\DateTime
    {
        return $this->last_connection;
    }

    public function setLastConnection(\DateTime $last_connection): self
    {
        $this->last_connection = $last_connection;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

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

    public function getHabitIds(): array
    {
        return $this->habit_ids;
    }

    public function addHabitId(string $habitId): self
    {
        $this->habit_ids[] = $habitId;
        return $this;
    }

    // Méthodes pour convertir entre ObjectId et string si nécessaire
    public function getGroupIdAsObjectId(): ?ObjectId
    {
        return $this->group ? new ObjectId($this->group) : null;
    }

    public function setGroupIdFromObjectId(?ObjectId $group): self
    {
        $this->group = $group ? (string) $group : null;

        return $this;
    }
}