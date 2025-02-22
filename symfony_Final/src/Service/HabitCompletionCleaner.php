<?php

namespace App\Service;

use App\Document\HabitCompletion;
use App\Document\Habit;
use App\Document\User;
use App\Document\PointLog;
use App\Document\Group;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;

class HabitCompletionCleaner
{
    private DocumentManager $dm;
    private LoggerInterface $logger;

    public function __construct(DocumentManager $dm, LoggerInterface $logger)
    {
        $this->dm = $dm;
        $this->logger = $logger;
    }

    public function clean()
    {
        $this->logger->info('Début du nettoyage des habit completions.');

        $habitCompletions = $this->dm->getRepository(HabitCompletion::class)->findAll();
        $this->resetCreatedHabitToday();

        foreach ($habitCompletions as $habitCompletion) {
            $this->dm->refresh($habitCompletion); 
        
            $habit = $habitCompletion->getHabit();
            $user = $habitCompletion->getUser();
            
            if ($habit === null || $user === null) {
                continue;
            }
        
            $periodicity = $habit->getPeriodicity();
            $completedAt = $habitCompletion->getCompletedAt();
            $isCompleted = $habitCompletion->isCompleted();
            $now = new \DateTime();
        
            if ($habitCompletion->getEndDate() === null) {
                $this->logger->error("End date is null for HabitCompletion with ID: " . $habitCompletion->getId());
                continue;
            }

            print "Comparing now ({$now->format('Y-m-d H:i:s')}) with endPeriod ({$habitCompletion->getEndDate()->format('Y-m-d H:i:s')})\n";
            if ($now < $habitCompletion->getEndDate()) {
                print "Habit period not finished yet, skipping.\n";
                continue;
            }
    
            if ($isCompleted) {
                print "Habit completed, resetting.\n";
                $this->resetCompletion($habitCompletion, $habit);
            } else {
                print "Habit not completed, applying penalty.\n";
                $this->applyPenalty($habitCompletion, $habit);
            }
        }
         
        $this->dm->flush();
        $this->logger->info('Fin du nettoyage des habit completions.');
    }

    private function resetCompletion($habitCompletion, $habit)
    {
        $habitCompletion->setCompleted(false);
        $habitCompletion->setCompletedAt(null);

        $habitCompletion->setStartDate((new \DateTime())->setTime(4, 0));

        if ($habit->getPeriodicity() === 'daily') {
            $habitCompletion->setEndDate((new \DateTime())->modify('+1 day')->setTime(4, 0));
        } elseif ($habit->getPeriodicity() === 'weekly') {
            $habitCompletion->setEndDate((new \DateTime())->modify('next Sunday')->setTime(4, 0));
        } elseif ($habit->getPeriodicity() === 'monthly') {
            $habitCompletion->setEndDate((new \DateTime())->modify('first day of next month')->setTime(4, 0));
        }
        $this->dm->persist($habitCompletion);
        $this->dm->flush();  

        $updatedHabitCompletion = $this->dm->getRepository(HabitCompletion::class)->find($habitCompletion->getId());

        if ($updatedHabitCompletion === null) {
            $this->logger->error("Impossible de rafraîchir HabitCompletion avec ID: " . $habitCompletion->getId());
            return;
        }

        $this->dm->refresh($updatedHabitCompletion);
    }

    private function applyPenalty($habitCompletion, $habit)
    {
        $pointsLog = new PointLog();
        $pointsLog->setUser($habitCompletion->getUser());
        if ($habit->getGroupId() !== null) {
            $group = $this->dm->getRepository(Group::class)->find($habit->getGroupIdAsObjectId());
            if ($group) {
                $pointsLog->setGroup($group);
            }
        }
        $pointsLog->setHabit($habit);

        if ($habit->getDifficulty() === 0) {
            $pointsLog->setPointsChange(-8);
            $pointsLog->setReason('Missed very easy habit');
        } elseif ($habit->getDifficulty() === 1) {
            $pointsLog->setPointsChange(-5);
            $pointsLog->setReason('Missed easy habit');
        } elseif ($habit->getDifficulty() === 2) {
            $pointsLog->setPointsChange(-3);
            $pointsLog->setReason('Missed medium habit');
        } elseif ($habit->getDifficulty() === 3) {
            $pointsLog->setPointsChange(-2);
            $pointsLog->setReason('Missed very hard habit');
        }

        if ($habit->getGroupId() !== null) {
            $group = $this->dm->getRepository(Group::class)->find($habit->getGroupIdAsObjectId());
            if ($group) {
                $group->setPoints($group->getPoints() + $pointsLog->getPointsChange());
                $this->dm->persist($group);
                if ($group->getPoints() < 0) {
                    $this->dm->remove($group);
                    $pointsLog = new PointLog();
                    $pointsLog->setUser($habitCompletion->getUser());
                    $pointsLog->setGroup($group);
                    $pointsLog->setHabit(null);
                    $pointsLog->setPointsChange(0);
                    $pointsLog->setReason('Group points below 0, group deleted');   
                }
            }
        } else {
            $habitCompletion->getUser()->setPoints($habitCompletion->getUser()->getPoints() + $pointsLog->getPointsChange());
        }
        $habitCompletion->setStartDate(new \DateTime());
        $habitCompletion->setStartDate((new \DateTime())->setTime(4, 0));

        if ($habit->getPeriodicity() === 'daily') {
            $habitCompletion->setEndDate((new \DateTime())->modify('+1 day')->setTime(4, 0));
        } elseif ($habit->getPeriodicity() === 'weekly') {
            $habitCompletion->setEndDate((new \DateTime())->modify('next Sunday')->setTime(4, 0));
        } elseif ($habit->getPeriodicity() === 'monthly') {
            $habitCompletion->setEndDate((new \DateTime())->modify('first day of next month')->setTime(4, 0));
        }
        
        $this->dm->persist($pointsLog);
        $this->dm->flush();  
    }

    private function resetCreatedHabitToday() {
        $userRepo = $this->dm->getRepository(User::class);
        $users = $userRepo->findAll();

        foreach($users as $user){
            if ($user->getCreatedHabitToday() === false) {
                continue;
            }
            if ($user->getGroup() !== null) {
                $group = $this->dm->getRepository(Group::class)->find($user->getGroup()->getId());
                if ($group) {
                    $group->setCreatedHabitToday(false);
                    $this->dm->persist($group);
                }
            }
            $user->setCreatedHabitToday(false);
            $this->dm->persist($user);
        }
        $this->dm->flush();
    }
}