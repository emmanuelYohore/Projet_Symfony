<?php

namespace App\Service;

use App\Document\HabitCompletion;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;

class HabitCompletionCleaner
{
    private DocumentManager $dm;
    private LoggerInterface $logger;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function clean()
    {
        // Pour les tests, exécutez immédiatement sans vérifier l'heure
        // $currentHour = (int) (new \DateTime())->format('G');
        // if ($currentHour !== 4) {
        //     return; // Ne rien faire si ce n'est pas 4 heures du matin
        // }

        $this->logger->info('Début du nettoyage des habit completions.');

        $habitCompletions = $this->dm->getRepository(HabitCompletion::class)->findAll();

        foreach ($habitCompletions as $habitCompletion) {
            $habit = $habitCompletion->getHabit();
            if ($habit === null) {
                $this->logger->info('HabitCompletion sans habit, ignoré.');
                continue; 
            }
            $completedAt = $habitCompletion->getCompletedAt();
            $periodicity = $habit->getPeriodicity(); 

            if ($periodicity === 'daily') {
                $yesterday = (new \DateTime('yesterday'))->setTime(0, 0);
                if ($completedAt < $yesterday) {
                    $this->dm->remove($habitCompletion);
                    $this->logger->info('HabitCompletion daily supprimé.');
                }

            } elseif ($periodicity === 'weekly') {
                $oneWeekAgo = (new \DateTime('-1 week'))->setTime(0, 0);
                if ($completedAt < $oneWeekAgo) {
                    $this->dm->remove($habitCompletion);
                    $this->logger->info('HabitCompletion weekly supprimé.');
                }
                
            } elseif ($periodicity === 'monthly') {
                $oneMonthAgo = (new \DateTime('-1 month'))->setTime(0, 0);
                if ($completedAt < $oneMonthAgo) {
                    $this->dm->remove($habitCompletion);
                    $this->logger->info('HabitCompletion monthly supprimé.');
                }
            } else {
                continue;
            }
        }

        $this->dm->flush();
        $this->logger->info('Fin du nettoyage des habit completions.');
    }
}