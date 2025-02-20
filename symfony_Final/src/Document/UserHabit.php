<?php

// src/Document/UserHabit.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(repositoryClass=UserHabitRepository::class)
 * @ODM\Id(strategy="NONE")
 */
class UserHabit
{
    /**
     * @ODM\ReferenceOne(targetDocument=User::class)
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument=Habit::class)
     */
    private $habit;
}