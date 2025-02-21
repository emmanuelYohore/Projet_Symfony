<?php

namespace App\Controller;

use App\Document\Habit;
use App\Document\User;
use App\Document\Group;
use App\Document\HabitCompletion;
use App\Document\PointLog;
use App\Form\HabitType;
use App\Form\GroupType;
use App\Form\UserType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/home', name: 'home_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
            if ($user) {
                $connected = true;
                $userHabits = $this->dm->getRepository(Habit::class)->findBy(['user' => $user]);

                $groupHabits = [];
                $groupRepository = $this->dm->getRepository(Group::class);
                $groups = $groupRepository->findAll();

                $userHabits = [];
                foreach ($user->getHabitIds() as $habitId) {
                    $habit = $this->dm->getRepository(Habit::class)->find($habitId);
                    if ($habit) {
                        $userHabits[] = $habit;
                    }
                }
                $user->habits = $userHabits;

                $completedHabits = $this->dm->getRepository(HabitCompletion::class)->findBy(['user' => $user]);
                $user->completedHabits = array_map(function($completion) {
                    return [
                        'habitId' => $completion->getHabit()->getId(),
                        'isCompleted' => $completion->isCompleted()
                    ];
                }, $completedHabits);

                return $this->render('home/index.html.twig', [
                    'user' => $user,
                    'userHabits' => $userHabits,
                    'groupHabits' => $groupHabits,
                    'groups' => $groups,
                    'connected' => $connected,
                ]);
            }
        } else {
            $groupRepository = $this->dm->getRepository(Group::class);
            $userRepository = $this->dm->getRepository(User::class);
            $habitRepository = $this->dm->getRepository(Habit::class);
            
            $groups = $groupRepository->findAll();
            $users = $userRepository->findAll();
            $habits = $habitRepository->findAll();

            foreach ($users as $user) {
            $userHabits = [];
                foreach ($user->getHabitIds() as $habitId) {
                    $habit = $this->dm->getRepository(Habit::class)->find($habitId);
                    if ($habit) {
                    $userHabits[] = $habit;
                    }
                }
                $user->habits = $userHabits; 
        
                $completedHabits = $this->dm->getRepository(HabitCompletion::class)->findBy(['user' => $user]);
                $user->completedHabits = array_map(function($completion) {
                return [
                        'habitId' => $completion->getHabit()->getId(),
                        'isCompleted' => $completion->isCompleted() // Assurez-vous que la méthode isCompleted() existe dans HabitCompletion
                    ];
                }, $completedHabits);
                }

        return $this->render('home/index.html.twig', [
            'users' => $users,
            'habits' => $habits,
            'userHabits' => $userHabits,
            'groups' => $groups,
            'connected' => $connected,
        ]);
        }
    }

    #[Route('/habitica-home/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, string $id): Response
    {
        $user = $this->dm->getRepository(User::class)->find($id);
        if ($user) {
            $this->dm->remove($user);
            $this->dm->flush();
        }

        return $this->redirectToRoute('home_index');
    }

    #[Route('/habitica-home/add_habit/{userId}/{groupId}', name: 'add_habit', methods: ['GET', 'POST'])]
    public function addHabit(Request $request, string $userId, ?string $groupId = null): Response
    {
        $user = $this->dm->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $habit = new Habit();
        $form = $this->createForm(HabitType::class, $habit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $habit->creator_id = $userId;
            $habit->group_id = $groupId;
            $this->dm->persist($habit);
            $user->addHabitId($habit->id);
            $user->setCreatedHabitToday(true);
            $this->dm->flush();

            $habitCompletion = new HabitCompletion();
            $habitCompletion->setUser($user);
            $habitCompletion->setHabit($habit);
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
            return $this->redirectToRoute('home_index');
        }

        return $this->render('home/add-habit.html.twig', [
            'form' => $form->createView(),
            'groupId' => $groupId,
        ]);
    }


    #[Route('/habitica-home/complete-habit/{userId}/{habitId}', name: 'complete_habit', methods: ['POST'])]
    public function completeHabit(Request $request, string $userId, string $habitId): Response
    {
        $user = $this->dm->getRepository(User::class)->find($userId);
        $habit = $this->dm->getRepository(Habit::class)->find($habitId);
        $group = $this->dm->getRepository(Group::class)->find($habit->group_id);
        
        if (!$user || !$habit) {
            throw $this->createNotFoundException('User or Habit not found');
        }

        $completed = $request->request->get('completed') === '1';

        if ($completed) {
            $habitCompletion = $this->dm->getRepository(HabitCompletion::class)->findOneBy(['user' => $user, 'habit' => $habit]);
            
            if (!$habitCompletion) {
                $habitCompletion = new HabitCompletion();
                $habitCompletion->setUser($user);
                $habitCompletion->setHabit($habit);
            }

            $habitCompletion->setCompleted(true);
            $habitCompletion->setCompletedAt(new \DateTime());

            $pointLog = new PointLog();
            $pointLog->setUser($user);

            if ($group) {
                $pointLog->setGroup($group);
            }

            $pointLog->setHabit($habit);


            if ($habit->difficulty === 0) {
                $pointLog->setPointsChange(1);
                $pointLog->setReason('Completed a very easy habit');

                $user->setPoints($user->getPoints() + 1);
            } elseif ($habit->difficulty === 1) {
                $pointLog->setPointsChange(2);
                $pointLog->setReason('Completed an easy habit');

                $user->setPoints($user->getPoints() + 2);
            } elseif ($habit->difficulty === 2) {
                $pointLog->setPointsChange(5);
                $pointLog->setReason('Completed a medium habit');

                $user->setPoints($user->getPoints() + 5);
            } elseif ($habit->difficulty === 3) {
                $pointLog->setPointsChange(10);
                $pointLog->setReason('Completed a very hard habit');

                $user->setPoints($user->getPoints() + 10);
            }

            $pointLog->setTimestamp(new \DateTime());

            $this->dm->persist($pointLog);
            $this->dm->persist($habitCompletion);
        } else {
            $habitCompletion = $this->dm->getRepository(HabitCompletion::class)->findOneBy(['user' => $user, 'habit' => $habit]);
            if ($habitCompletion && $habitCompletion->isCompleted()) {
                $habitCompletion->setCompleted(false);
                $habitCompletion->setCompletedAt(null);
                $pointLog = $this->dm->getRepository(PointLog::class)->findOneBy(['user' => $user, 'habit' => $habit]);
                if ($pointLog) {
                    $this->dm->remove($pointLog);
                    if ($habit->difficulty === 0) {
                        $user->setPoints($user->getPoints() - 1);
                    } elseif ($habit->difficulty === 1) {
                        $user->setPoints($user->getPoints() - 2);
                    } elseif ($habit->difficulty === 2) {
                        $user->setPoints($user->getPoints() - 5);
                    } elseif ($habit->difficulty === 3) {
                        $user->setPoints($user->getPoints() - 10);
                    }
                }
            }

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'userHabits' => $userHabits,
            'groupHabits' => $groupHabits
        ]);
        }
    }
    

    #[Route('/habitica-home/delete_habit/{habitId}', name: 'delete_habit', methods: ['POST'])]
    public function deleteHabit(Request $request, string $habitId): Response
    {
        $habit = $this->dm->getRepository(Habit::class)->find($habitId);
        if ($habit) {
            $user = $this->dm->getRepository(User::class)->find($habit->creator_id);
            if ($user) {
                $habitCompletions = $this->dm->getRepository(HabitCompletion::class)->findBy(['habit' => $habit]);
                foreach ($habitCompletions as $habitCompletion) {
                    $this->dm->remove($habitCompletion);
                }

                $user->removeHabitId($habitId);
                $this->dm->persist($user);

                $this->dm->remove($habit);
                $this->dm->flush();
            }
        }

        return $this->redirectToRoute('home_index');
    }
}
