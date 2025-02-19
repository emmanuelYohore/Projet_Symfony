<?php

// src/Document/Group.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=GroupRepository::class)
 */
class Group
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(type="string")
     */
    private $name;

    /**
     * @ODM\Field(type="int")
     */
    private $totalPoints;

    /**
     * @ODM\ReferenceMany(targetDocument=User::class, mappedBy="group")
     */
    private $users;
}
