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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

    #[Route('/habitica-home', name: 'home_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SessionInterface $session): Response
    {   
        $userRepository = $this->dm->getRepository(User::class);
        $groupRepository = $this->dm->getRepository(Group::class);
        $habitRepository = $this->dm->getRepository(Habit::class);
        
        $users = $userRepository->findAll();
        $habits = $habitRepository->findAll();
        $groups = $groupRepository->findAll();

        $id = $session->get('connected_user');
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($id) {
            $invit = $this->getInvitations($session);
            $logs = $this->getPointsLog($session);
        }
        

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get("profile_picture")->getData();

            if ($profilePicture) {
                $originalFileName = pathinfo($profilePicture->getClientOriginalName(),PATHINFO_FILENAME);
                $newFileName = $originalFileName . '-' . uniqid() . '.' . $profilePicture->guessExtension();
                try {
                    $profilePicture->move(
                        $this->getParameter('picture_directory'), // Assure-toi d’avoir ce paramètre configuré dans services.yaml
                        $newFileName
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_register');
                }
                
                $user->setProfilePicture($newFileName);
            }
            $user->setPassword(password_hash($user->getPassword(),PASSWORD_BCRYPT));
            $this->dm->persist($user);
            $session->set('connected_user',$user->getId());
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
        'logs' => $logs,
        'invits' => $invit,
        'user' => $session->get('connected_user') ? $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]) : null,
    ]);
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
        $group = $this->dm->getRepository(Group::class)->find($habit->group_id);
        
        if (!$user || !$habit) {
            throw $this->createNotFoundException('User or Habit not found');
        }

        $completed = $request->request->get('completed') === '1';

        if ($completed) {
            $habitCompletion = new HabitCompletion();
            $habitCompletion->setUser($user);
            $habitCompletion->setHabit($habit);
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
            if ($habitCompletion) {
                $this->dm->remove($habitCompletion);
                $pointLog = $this->dm->getRepository(PointLog::class)->findOneBy(['user' => $user, 'habit' => $habit]);
                if ($pointLog) {
                    $this->dm->remove($pointLog);
                    $user->setPoints($user->getPoints() - $pointLog->getPointsChange());
                }
            }
        }
        

        $this->dm->flush();

        return $this->redirectToRoute('home_index');
    }

    #[Route('/habitica-home/delete_habit/{habitId}', name: 'delete_habit', methods: ['POST'])]
    public function deleteHabit(Request $request, string $habitId): Response
    {
        $habit = $this->dm->getRepository(Habit::class)->find($habitId);
        $habitCompletions = $this->dm->getRepository(HabitCompletion::class)->findBy(['habit' => $habit]);
        if ($habit) {
            foreach ($habitCompletions as $habitCompletion) {
                $this->dm->remove($habitCompletion);
            }
            $this->dm->remove($habit);
            $this->dm->flush();
        }

        return $this->redirectToRoute('home_index');
    }



    private function getInvitations(SessionInterface $session): array
    {
        if (!$session->get('connected_user'))
        {
            return [];
        }

        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
       
        $allInvitations = $this->dm->getRepository(Invitation::class)->findAll();
        $invitations = [];
        
        foreach($allInvitations as $invit)
        {
            if ($invit->getReceiver() == $user)
            {
                array_push($invitations,$invit);
            }
        }
        return $invitations;

    }

    private function getPointsLog(SessionInterface $session) : array
    {
        if (!$session->get('connected_user'))
        {
            return [];
        }

        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
       
        $allPointLog = $this->dm->getRepository(PointLog::class)->findAll();
        $pointLog = [];
        $groupUser = $user->getGroup();
        foreach($allPointLog as $log)
        {
            if ($log->getUser() == $user || $log->getGroup() == $groupUser)
            {
                array_push($pointLog,$log);
            }
        }
        return $pointLog;
    }
}
