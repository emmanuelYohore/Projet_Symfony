<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;


#[ODM\Document(collection: 'habits')]
class Habit
{
    #[ODM\Id(strategy: 'AUTO')]
    public ?string $id = null;

    #[ODM\Field(type: 'string')]
    public string $name;

    #[ODM\Field(type: 'string')]
    public string $description;

    #[ODM\Field(type: 'int')]
    public int $difficulty;

    #[ODM\Field(type: 'string')]
    public string $color;

    #[ODM\Field(type: 'string')]
    public string $periodicity;

    #[ODM\Field(type: 'string')]
    public string $creator_id;

    #[ODM\Field(type: 'string', nullable: true)]
    public ?string $group_id = null;

    public function __construct()
    {
        // Initialisation des valeurs par défaut si nécessaire
    }

    // Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id):self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPeriodicity(): ?string
    {
        return $this->periodicity;
    }

    public function setPeriodicity(string $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getCreatorId(): ?string
    {
        return $this->creator_id;
    }

    public function setCreatorId(string $creator_id): self
    {
        $this->creator_id = $creator_id;

        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->group_id;
    }

    public function setGroupId(?string $group_id): self
    {
        $this->group_id = $group_id;

        return $this;
    }

    public function getCreatorIdAsObjectId(): ?ObjectId
    {
        return $this->creator_id ? new ObjectId($this->creator_id) : null;
    }

    public function setCreatorIdFromObjectId(?ObjectId $creator_id): self
    {
        $this->creator_id = $creator_id ? (string) $creator_id : null;

        return $this;
    }

    public function getGroupIdAsObjectId(): ?ObjectId
    {
        return $this->group_id ? new ObjectId($this->group_id) : null;
    }

    public function setGroupIdFromObjectId(?ObjectId $group_id): self
    {
        $this->group_id = $group_id ? (string) $group_id : null;

        return $this;
    }
}