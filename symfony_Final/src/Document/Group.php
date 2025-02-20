<?php
declare(strict_types=1);
// src/Document/Group.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'groups')]
#[ODM\Index(keys: ['name' => 'asc'])]

class Group
{
    
    #[ODM\Id(strategy: 'AUTO')]
    private $id;

    #[ODM\Field(type: 'string')]
    private $name;

    #[ODM\Field(type: "int")]
    private $totalPoints;

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
}