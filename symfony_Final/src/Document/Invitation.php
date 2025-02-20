<?php
declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: 'invitation')]
#[ODM\Index(keys: ['sender' => 'asc'])]

class Invitation
{
    #[ODM\Id(strategy: "AUTO")]
    private $id;

    #[ODM\Field(type: 'string')]
    private $sender;

    #[ODM\Field(type: 'string')]
    private $receiver;

    #[ODM\Field(type: 'string')]
    private $group;

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
    public function getSender() :?string
    {
        return $this->sender;
    }
    public function getReceiver() :?string
    {
        return $this->receiver;
    }
    public function getGroup() :?string
    {
        return $this->group;
    }
    public function setSender(string $sender) : self
    {
        $this->sender = $sender;
        return $this;
    }
    public function setReceiver(string $receiver) : self
    {
        $this->receiver = $receiver;
        return $this;
    }
    public function setGroup(string $group) : self
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