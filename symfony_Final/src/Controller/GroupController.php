<?php

namespace App\Controller;
use App\Document\Group;
use App\Document\Invitation;
use App\Document\User;
use App\Document\UserHabit;
use App\Document\PointLog;

use App\Document\Habit;
use App\Document\HabitCompletion;
use App\Form\UserType;
use App\Form\GroupType;
use App\Form\HabitType;
use App\Controller\HomeController;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\BSON\ObjectId;


class GroupController extends AbstractController
{
    private DocumentManager $dm;
    private LoggerInterface $logger;
    public function __construct(DocumentManager $dm, LoggerInterface $logger)
    {
        $this->dm = $dm;
        $this->logger = $logger;
    }

    #[Route('/group', name: 'app_group')]
    public function index(Request $request,SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }

        if (!$session->get('connected_user'))
        {
            return $this->redirectToRoute('home_index');
        }
        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        if (!$user->getGroup()) 
        {
            return $this->redirectToRoute('create_group', [
                'connected' => $connected,
            ]);
        } else {
            return $this->redirectToRoute('view_group', [
                'connected' => $connected,
            ]);
        }
    }

    #[Route("/create_group", name: "create_group")]
    public function create(Request $request, SessionInterface $session): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        $userId = $session->get('connected_user');
        $connected = false;
        $control = new HomeController($this->dm);
        $control->getNewNotifs($this->dm->getRepository(User::class)->find($userId),$session);
        $logs = $this->dm->getRepository(PointLog::class)->findBy(['id' => ['$in' => $session->get('logs') ? $session->get('logs') : []]]);
        $invits = $this->dm->getRepository(Invitation::class)->findBy(['id' => ['$in' => $session->get('invit')? $session->get('invit') : []]]);
        $notifs = $control->getOrderedNotifs($logs,$invits,$this->dm->getRepository(User::class)->find($userId),$session);
        $connected_user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);

        
        if ($userId) {
            $connected = true;
        }

        if ($form->isSubmitted() && $form->isValid())
        {

            $identifier = $form->get('emails')->getData();
            if (str_contains($identifier,'@')) {
                $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $identifier]);
            } else {
                $user = $this->dm->getRepository(User::class)->findOneBy(['username' => $identifier]);
            }        

            $group->setCreator($connected_user);
            $this->dm->persist($group);
            $connected_user->setGroup($group);
            $this->dm->persist($connected_user);
            $this->dm->flush();
            if ($user && !$user->getGroup())
            {
                $this->createInvitation($connected_user,$user,$group);
            }
            return $this->redirectToRoute('view_group', [
                'connected' => $connected,
            ]);
        }

        return $this->render('group/createGroup.html.twig', [
            'form' => $form->createView(),
            'connected' => $connected,
            'logs' => $logs,
            'invitations' => $invits,
            'allNotifs' => $notifs,
        ]);
    }

    #[Route('/view_group', name: "view_group")]
    public function view(Request $request, SessionInterface $session): Response
    {
        $userId = $session->get('connected_user');
        $connected = false;
        $control = new HomeController($this->dm);
        $control->getNewNotifs($this->dm->getRepository(User::class)->find($userId),$session);
        $logs = $this->dm->getRepository(PointLog::class)->findBy(['id' => ['$in' => $session->get('logs') ? $session->get('logs') : []]]);
        $invits = $this->dm->getRepository(Invitation::class)->findBy(['id' => ['$in' => $session->get('invit')? $session->get('invit') : []]]);
        $notifs = $control->getOrderedNotifs($logs,$invits,$this->dm->getRepository(User::class)->find($userId),$session);
       
        if ($userId) {
            $connected = true;
        }

        $connected_user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        $group = $connected_user->getGroup();
        if (!$group)
        {
            return $this->redirectToRoute('create_group');
        }
        $formAddUser = $this->createForm(GroupType::class, $group);
        $formAddUser->handleRequest($request);

        $habit = new Habit();
        $formAddTask = $this->createForm(HabitType::class, $habit);
        $formAddTask->handleRequest($request);
        if ($formAddUser->isSubmitted() && $formAddUser->isValid())
        {
            $identifier = $formAddUser->get('emails')->getData();
            if (str_contains($identifier,'@')) {
                $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $identifier]);
            } else {
                $user = $this->dm->getRepository(User::class)->findOneBy(['username' => $identifier]);
            }
            if ($user)
            {
                $this->createInvitation($connected_user,$user,$group);
            }
            
            return $this->redirectToRoute('view_group', [
                'connected' => $connected,
            ]);
        }

        if ($formAddTask->isSubmitted() && $formAddTask->isValid())
        {
            $habit->setId($habit->getId());
            $habit->setCreatorId($connected_user->getId());
            $habit->setGroupId($group->getId());
            $this->dm->persist($habit);
            $connected_user->addHabitId($habit->getId());
            $this->dm->persist($connected_user);
            $this->dm->flush();
            return $this->redirectToRoute('view_group', [
                'connected' => $connected,
            ]);
        }
        
        $this->dm->flush();
        $groupUser = $this->getUserByGroup($group);
        $groupHabit = $this->getHabitsGroup($group);
        $connected_user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        $completedHabits = $this->dm->getRepository(HabitCompletion::class)->findBy(['user' => $connected_user]);
        $connected_user->completedHabits = array_map(function($completion) {
            return [
                'habitId' => $completion->getHabit()->getId(),
                'isCompleted' => $completion->isCompleted()
            ];
        }, $completedHabits);
        return $this->render('group/viewGroup.html.twig', [
            'formAddUser' => $formAddUser->createView(),
            'formAddTask' => $formAddTask->createView(),
            'group' => $group,
            'groupUser' => $groupUser,
            'groupHabit' => $groupHabit,
            'connected_user' => $connected_user,
            'completed_task' => $this->getCompletedTask($group),
            'connected' => $connected,
            'logs' => $logs,
            'invitations' => $invits,
            'allNotifs' => $notifs,
        ]);
    }
    #[Route('/view_group/delete_task/{taskId}', name: 'delete_task', methods: ['POST'])]
    public function deleteTask(Request $request,SessionInterface $session, string $taskId) :Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }

        $task = $this->dm->getRepository(Habit::class)->find($taskId);
        $taskCompletions = $this->dm->getRepository(HabitCompletion::class)->findBy(['habit' => $task ? $task : null]);
        if ($task) {
            foreach ($taskCompletions as $habitCompletion) {
                $this->dm->remove($habitCompletion);
            }
            $this->dm->remove($task);
            $this->dm->flush();
        }
        return $this->redirectToRoute('view_group', [
            'connected' => $connected,
        ]);
    }

    #[Route('/view_group/complete_task/{taskId}', name:"complete_task", methods: ['POST'])]
    public function completeTask(Request $request, SessionInterface $session, string $taskId):Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }

        $user = $this->dm->getRepository(User::class)->find($session->get('connected_user'));
        $task = $this->dm->getRepository(Habit::class)->find($taskId);
        $group = $this->dm->getRepository(Group::class)->find($task->getGroupId());
        $taskComplete = new HabitCompletion();
        $taskComplete->setUser($user);
        $taskComplete->setHabit($task);
        $taskComplete->setCompleted(true);
            
        $pointLog = new PointLog();
        $pointLog->setUser($user);
        $pointLog->setGroup($group);
        $pointLog->setHabit($task);

        switch ($task->difficulty)
        {
            case 0:
                $pointLog->setPointsChange(1);
                $pointLog->setReason('Completed a ver easy Habit');

                $group->setPoints($group->getPoints()+1);
                break;
            case 1:
                $pointLog->setPointsChange(2);
                $pointLog->setReason('Completed an easy habit');

                $group->setPoints($group->getPoints() + 2);
                break;
            case 2:
                $pointLog->setPointsChange(5);
                $pointLog->setReason('Completed a medium habit');

                $group->setPoints($group->getPoints() + 5);
                break;
            case 3:
                $pointLog->setPointsChange(10);
                $pointLog->setReason('Completed a very hard habit');

                $group->setPoints($group->getPoints() + 10);
                break;
            default:
                break;
        }

        $this->dm->persist($pointLog);
        $this->dm->persist($taskComplete);
        
        $this->dm->flush();

        return $this->redirectToRoute('view_group', [
            'connected' => $connected,
        ]);

    }
    private function getUserByGroup(?Group $group): array
    {
        $users = $this->dm->getRepository(User::class)->findAll();
        $groupUser = [];
        foreach ($users as $user)
        {
            if ($user->getGroup() == $group)
            {
                array_push($groupUser,$user);
            }
        }
        return $groupUser;
    }

    private function createInvitation(User $sender,User $receiver,Group $group)
    {
        $invitation = new Invitation();
        $invitation->setGroup($group);
        $invitation->setSender($sender);
        $invitation->setReceiver($receiver);
        $invitation->setTimestamp();
        $this->dm->persist($invitation);
        $this->dm->flush();
    }

    private function getHabitsGroup(Group $group): array
    {
        $habits = $this->dm->getRepository(Habit::class)->findAll();
        $groupHabits = [];
        foreach($habits as $habit)
        {
            if ($habit->getGroupId() == $group->getId())
            {
                array_push($groupHabits, $habit);
            }
        }
        return $groupHabits;
    }

    private function getCompletedTask(Group $group): array
    {
        $habitsCompleted = $this->dm->getRepository(HabitCompletion::class)->findAll();
        $taskCompleteGroup = [];
        foreach($habitsCompleted as $task)
        {
            if (!$task->habit){
                $this->dm->remove($task);
                continue;
            }
            if($task->habit->getGroupId() == $group->getId())
            {
                array_push($taskCompleteGroup, $task->getHabit());
            }
        }
        return $taskCompleteGroup;
    }
}