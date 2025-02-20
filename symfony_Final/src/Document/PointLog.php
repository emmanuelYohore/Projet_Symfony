<?php
declare(strict_types=1);
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;
use \DateTime;
use Egulias\EmailValidator\Result\Reason\Reason;

#[ODM\Document(collection: 'points_log')]
#[ODM\Index(keys: ['user_id' => 'asc'])]

class PointsLog
{

    #[ODM\Id(strategy:'AUTO')]
    private $id;

    #[ODM\Field(type:'string')]
    private $user;

    #[ODM\Field(type:"string", nullable: true)]
    private $group;

    #[ODM\Field(type:'int')]
    private $pointsChange;

    #[ODM\Field(type:'string')]
    private $reason;

    #[ODM\Field(type:"timestamp")]
    private $timestamp;

    public function construct()
    {
        $now = new DateTime();
        $this->timestamp = $now->format('d-m-Y H:i:s');
    }

    public function getId() :?int
    {
        return $this->id;
    }

    public function getUser() :string
    {
        return $this->user;
    }
    public function setUser(string $user) :self
    {
        $this->user = $user;
        return $this;
    }
    
    public function getGroup() :?string
    {
        return $this->group;
    }
    public function setGroup(string $group) :self
    {
        $this->group = $group;
        return $this;
    }

    public function getPoints() :int
    {
        return $this->pointsChange;
    }
    public function setPoints(int $points) :self
    {
        $this->pointsChange = $points;
        return $this;
    }

    public function getReason() :string
    {
        return $this->reason;
    }
    public function setReason(string $reason) :self
    {
        $this->reason = $reason;
        return $this;
    }

    public function getTime() :string
    {
        return $this->timestamp;
    }

}
