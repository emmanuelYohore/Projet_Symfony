<?php

// src/Document/Habit.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=HabitRepository::class)
 */
class Habit
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
     * @ODM\Field(type="string")
     */
    private $description;

    /**
     * @ODM\Field(type="int")
     */
    private $difficulty;

    /**
     * @ODM\Field(type="string")
     */
    private $color;

    /**
     * @ODM\Field(type="string")
     */
    private $periodicity;

    /**
     * @ODM\ReferenceOne(targetDocument=User::class)
     */
    private $creator;

    /**
     * @ODM\ReferenceOne(targetDocument=Group::class, nullable=true)
     */
    private $group;
}