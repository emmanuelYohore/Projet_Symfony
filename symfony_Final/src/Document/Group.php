<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ODM\Document(collection: 'groups')]
class Group
{
    #[ODM\Id(strategy: 'AUTO')]
    public ?string $id = null;

    #[ODM\Field(type: 'string')]
    public string $name;

    #[ODM\Field(type: 'int')]
    public int $totalPoints;

    #[ODM\ReferenceMany(targetDocument: User::class, mappedBy: 'group')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    // Getters and setters
    public function getId(): ?string
    {
        return $this->id;
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

    public function getTotalPoints(): ?int
    {
        return $this->totalPoints;
    }

    public function setTotalPoints(int $totalPoints): self
    {
        $this->totalPoints = $totalPoints;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGroup() === $this) {
                $user->setGroup(null);
            }
        }

        return $this;
    }
}