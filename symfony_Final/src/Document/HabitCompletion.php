<?php

// src/Document/HabitCompletion.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=HabitCompletionRepository::class)
 */
class HabitCompletion
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
     * @ODM\ReferenceOne(targetDocument=Habit::class)
     */
    private $habit;

    /**
     * @ODM\Field(type="date")
     */
    private $completedAt;
}