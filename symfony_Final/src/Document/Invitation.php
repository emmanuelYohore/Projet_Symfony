<?php
declare(strict_types=1);
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'invitation')]

class Invitation
{
    #[ODM\Id(strategy: "AUTO")]
    private $id;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    private ?User $sender = null;

    #[ODM\ReferenceOne(targetDocument: User::class)]
    private ?User $receiver = null;

    #[ODM\ReferenceOne(targetDocument:Group::class)]
    private ?Group $group = null;

    #[ODM\Field(type: 'string')]
    private $status;

    public function __contruct()
    {
        $this->status = "pending";
    }

    public function getId() :?string
    {
        return $this->id;
    }
    public function getSender() :?User
    {
        return $this->sender;
    }
    public function getReceiver() :?User
    {
        return $this->receiver;
    }
    public function getGroup() :?Group
    {
        return $this->group;
    }
    public function setSender(?User $sender) : self
    {
        $this->sender = $sender;
        return $this;
    }
    public function setReceiver(?User $receiver) : self
    {
        $this->receiver = $receiver;
        return $this;
    }
    public function setGroup(?Group $group) : self
    {
        $this->group = $group;
        return $this;
    }
    public function getStatus() :?string
    {
        return $this->status;
    }

    public function setStatus(string $status) : self
    {
        $this->status = $status;
        return $this;
    }

}
