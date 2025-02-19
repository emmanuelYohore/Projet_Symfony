<?php

// src/Document/Invitation.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=InvitationRepository::class)
 */
class Invitation
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument=User::class)
     */
    private $sender;

    /**
     * @ODM\ReferenceOne(targetDocument=User::class)
     */
    private $receiver;

    /**
     * @ODM\ReferenceOne(targetDocument=Group::class)
     */
    private $group;

    /**
     * @ODM\Field(type="string")
     */
    private $status;
}
