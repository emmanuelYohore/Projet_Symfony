<?php

// src/Document/PointsLog.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=PointsLogRepository::class)
 */
class PointLog
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument=User::class)
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument=Group::class, nullable=true)
     */
    private $group;

    /**
     * @ODM\Field(type="int")
     */
    private $pointsChange;

    /**
     * @ODM\Field(type="string")
     */
    private $reason;

    /**
     * @ODM\Field(type="date")
     */
    private $timestamp;
}