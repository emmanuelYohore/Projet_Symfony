<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Group;
use App\Document\Invitation;
use App\Document\User;
use App\Document\UserHabit;
use App\Document\PointLog;
use App\Document\Habit;
use App\Document\HabitCompletion;
use App\Form\UserType;
use App\Form\HabitType;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    private DocumentManager $dm;
    private LoggerInterface $logger;
    public function __construct(DocumentManager $dm, LoggerInterface $logger)
    {
        $this->dm = $dm;
        $this->logger = $logger;
    }

    #[Route('/habitica-home', name: 'home_index')]
public function index(Request $request): Response
{
    $users = $this->dm->getRepository(User::class)->findAll();
    $habits = $this->dm->getRepository(Habit::class)->findAll();
    $groups = [];

    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->dm->persist($user);
        $this->dm->flush();

        return $this->redirectToRoute('home_index');
    }

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
        $user->completedHabits = array_map(fn($completion) => $completion->getHabit()->getId(), $completedHabits);
    }

    return $this->render('habitica/index.html.twig', [
        'users' => $users,
        'habits' => $habits,
        'groups' => $groups,
        'form' => $form->createView(),
    ]);
}

    #[Route('/habitica-home/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, string $id): Response
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
            $this->dm->flush();

            return $this->redirectToRoute('home_index');
        }

        return $this->render('habitica/add-habit.html.twig', [
            'form' => $form->createView(),
            'groupId' => $groupId,
        ]);
    }

    #[Route('/habitica-home/complete-habit/{userId}/{habitId}', name: 'complete_habit', methods: ['POST'])]
    public function completeHabit(Request $request, string $userId, string $habitId): Response
    {
        $user = $this->dm->getRepository(User::class)->find($userId);
        $habit = $this->dm->getRepository(Habit::class)->find($habitId);

        if (!$user || !$habit) {
            throw $this->createNotFoundException('User or Habit not found');
        }

        $completed = $request->request->get('completed') === '1';

        if ($completed) {
            $habitCompletion = new HabitCompletion();
            $habitCompletion->setUser($user);
            $habitCompletion->setHabit($habit);
            $habitCompletion->setCompletedAt(new \DateTime());

            $this->dm->persist($habitCompletion);
        } else {
            $habitCompletion = $this->dm->getRepository(HabitCompletion::class)->findOneBy(['user' => $user, 'habit' => $habit]);
            if ($habitCompletion) {
                $this->dm->remove($habitCompletion);
            }
        }
        

        $this->dm->flush();

        return $this->redirectToRoute('home_index');
    }
}