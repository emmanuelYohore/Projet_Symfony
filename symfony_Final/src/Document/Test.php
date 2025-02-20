<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'test')]
class Test
{
    #[ODM\Id(strategy: 'AUTO')]
    public ?string $id = null;

    #[ODM\Field(type: 'string')]
    public string $nom;

    #[ODM\Field(type: 'int')]
    public int $age;

    public function __construct(string $nom, int $age)
    {
        $this->nom = $nom;
        $this->age = $age;
    }

    // Getters et setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }
}
