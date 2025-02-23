<?php

namespace App\Controller;

use App\Document\Habit;
use App\Document\User;
use App\Document\Group;
use App\Document\HabitCompletion;
use App\Document\PointLog;
use App\Document\Invitation;
use App\Form\HabitType;
use App\Form\GroupType;
use App\Form\UserType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\CodePointString;

class HomeController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/', name:"rien", methods:['GET','POST'])]
    public function redirection(Request $request) : Response
    {
        return $this->redirectToRoute('home_index');
    }
    #[Route('/home', name: 'home_index', methods: ['GET', 'POST'])]
    public function index(Request $request,SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;
        $userHabits = [];
        $groupHabits = [];
        $groups = [];
        $this->getNewNotifs($this->dm->getRepository(User::class)->find($userId),$session);
        $logs = $this->dm->getRepository(PointLog::class)->findBy(['id' => ['$in' => $session->get('logs') ? $session->get('logs') : []]]);
        $invits = $this->dm->getRepository(Invitation::class)->findBy(['id' => ['$in' => $session->get('invit')? $session->get('invit') : []]]);
        $notifs = $this->getOrderedNotifs($logs,$invits,$this->dm->getRepository(User::class)->find($userId),$session);
        if ($userId) {
            $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
            if ($user) {
                $connected = true;
                $userHabits = $this->dm->getRepository(Habit::class)->findBy(['user' => $user]);

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

                if ($user->getGroup()) {
                    $group = $user->getGroup();
                    $creator = $this->dm->getRepository(User::class)->find($group->getCreator()->getId());
                    $group->setCreator($creator);
                }
                
                if ($user->getGroup()) {
                    $groupHabits = $this->dm->getRepository(Habit::class)->findBy(['group_id' => $user->getGroup()->getId()]);
                }
                return $this->render('home/index.html.twig', [
                    'user' => $user,
                    'userHabits' => $userHabits,
                    'groupHabits' => $groupHabits,
                    'groups' => $groups,
                    'connected' => $connected,
                    'logs' => $logs,
                    'invitations' => $invits,
                    'allNotifs' => $notifs,
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
            'logs' => [],
            'invit' => [],
            'notifs' => [],
        ]);
        }
    }

    #[Route('/habitica-home/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, string $id ,SessionInterface $session): Response
    {   

        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }

        $user = $this->dm->getRepository(User::class)->find($id);
        if ($user) {
            $this->dm->remove($user);
            $this->dm->flush();
        }

        return $this->redirectToRoute('home_index', [
            'connected' => $connected,
        ]);
    }

    #[Route('/habitica-home/add_habit/{userId}/{groupId?}', name: 'add_habit', methods: ['GET', 'POST'])]
    public function addHabit(Request $request, string $userId, ?string $groupId = null ,SessionInterface $session): Response
    {   

        $userId = $session->get('connected_user');
        $connected = false;
        $this->getNewNotifs($this->dm->getRepository(User::class)->find($userId),$session);
        $logs = $this->dm->getRepository(PointLog::class)->findBy(['id' => ['$in' => $session->get('logs') ? $session->get('logs') : []]]);
        $invits = $this->dm->getRepository(Invitation::class)->findBy(['id' => ['$in' => $session->get('invit')? $session->get('invit') : []]]);
        $notifs = $this->getOrderedNotifs($logs,$invits,$this->dm->getRepository(User::class)->find($userId),$session);
        
        if ($userId) {
            $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
            if ($user) {
                $connected = true;
            }
        }

        $user = $this->dm->getRepository(User::class)->find($userId);

        $group = $groupId ? $this->dm->getRepository(Group::class)->find($groupId) : null;

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
            if ($groupId){
                $group->setCreatedHabitToday(true);
                $this->dm->persist($group);
            } else {
                $user->addHabitId($habit->id);
                $user->setCreatedHabitToday(true);
            }
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
            'connected' => $connected,
            'logs' => $logs,
            'invitations' => $invits,
            'allNotifs' => $notifs,
        ]);
    }


    #[Route('/habitica-home/complete-habit/{userId}/{habitId}/{in_group?}', name: 'complete_habit', methods: ['POST'])]
    public function completeHabit(Request $request, string $userId, string $habitId, ?bool $in_group ,SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
            if ($user) {
                $connected = true;
            }
        }

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

            switch ($habit->difficulty) {
                case 0:
                    $points = 1;
                    $reason = 'Completed a very easy habit';
                    break;
                case 1:
                    $points = 2;
                    $reason = 'Completed an easy habit';
                    break;
                case 2:
                    $points = 5;
                    $reason = 'Completed a medium habit';
                    break;
                case 3:
                    $points = 10;
                    $reason = 'Completed a very hard habit';
                    break;
                default:
                    $points = 0;
                    $reason = 'Unknown difficulty';
            }

            $pointLog->setPointsChange($points);
            $pointLog->setReason($reason);

            if($habit->getGroupId() !== null){
                $group = $this->dm->getRepository(Group::class)->find($habit->getGroupIdAsObjectId());
                if($group){
                    $pointLog->setGroup($group);
                    $group->setPoints($group->getPoints() + $points);
                    $this->dm->persist($group);
                }
            } else {
                $user->setPoints($user->getPoints() + $points);
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
                    if($habit->getGroupId() !== null){
                            $group = $this->dm->getRepository(Group::class)->find($habit->getGroupIdAsObjectId());
                            switch ($habit->difficulty) {
                                case 0: $group->setPoints($group->getPoints() - 1); break;
                                case 1: $group->setPoints($group->getPoints() - 2); break;
                                case 2: $group->setPoints($group->getPoints() - 5); break;
                                case 3: $group->setPoints($group->getPoints() - 10); break;
                            }
                        } else {
                            switch ($habit->difficulty) {
                                case 0: $user->setPoints($user->getPoints() - 1); break;
                                case 1: $user->setPoints($user->getPoints() - 2); break;
                                case 2: $user->setPoints($user->getPoints() - 5); break;
                                case 3: $user->setPoints($user->getPoints() - 10); break;
                            }
                        }
                    }
                }
            }
        

        $this->dm->flush();

        if ($in_group == true) {
            return $this->redirectToRoute('view_group', [
                'connected' => $connected,
            ]);
        } else {
            return $this->redirectToRoute('home_index', [
                'connected' => $connected,
            ]);
        }

        
    }

    

    #[Route('/habitica-home/delete_habit/{habitId}', name: 'delete_habit', methods: ['POST'])]
    public function deleteHabit(Request $request, string $habitId ,SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
            if ($user) {
                $connected = true;
            }
        }   
        
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

        return $this->redirectToRoute('home_index', [
            'connected' => $connected,
        ]);
    }
    #[Route('/accept/{groupId}/{invitId}', name: "accept_invit", methods: ['POST'])]
    public function acceptInvitation(Request $request, SessionInterface $session, string $groupId, string $invitId) : Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }

        $group = $this->dm->getRepository(Group::class)->find($groupId);
        $user = $this->dm->getRepository(User::class)->find($session->get('connected_user'));
        $user->setGroup($group);
        $this->removeInvitation($invitId);
        $pointLog = new PointLog();
        $pointLog->setPointsChange(0);
        $pointLog->setUser($user);
        $pointLog->setGroup($group);
        $pointLog->setReason($user->getUsername() . " joined your group " . $group->getName() . " !" );
        $this->dm->persist($pointLog);
        $this->dm->persist($user);
        $this->dm->flush();
        return $this->redirectToRoute('home_index', [
            'connected' => $connected
        ]);
    }

    #[Route('/decline/{groupId}/{invitId}', name: "decline_invit", methods: ['POST'])]
    public function declineInvitation(Request $request, SessionInterface $session, string $groupId, string $invitId) : Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;

        if ($userId) {
            $connected = true;
        }
        
        $group = $this->dm->getRepository(Group::class)->find($groupId);
        $invit = $this->dm->getRepository(Invitation::class)->find($invitId);
        $user = $this->dm->getRepository(User::class)->find($session->get('connected_user'));
        $pointLog = new PointLog();
        $pointLog->setPointsChange(0);
        $pointLog->setUser($invit->getSender());
        $pointLog->setGroup($group);
        $pointLog->setReason($user->getUsername() . " declined your invitation.");
        $this->dm->persist($pointLog);
        $this->removeInvitation($invitId);
        $this->dm->remove($invit);
        $this->dm->flush();
        return $this->redirectToRoute('home_index', [
            'connected' => $connected
        ]);

        
    }
    private function removeInvitation(string $invitation)
    {
        $invit = $this->dm->getRepository(Invitation::class)->find($invitation);
        $this->dm->remove($invit);
    }

    public function getOrderedNotifs(array $logs, array $invits,?User $user, SessionInterface $session):array 
    {
        if (!$user) {
            return [];
        }

        $allNotifs = array_merge($logs, $invits);
        usort($allNotifs, function ($a, $b) {
            return $b->getTimestamp() <=> $a->getTimestamp();
        });
        return $allNotifs;
    }

    public function getNewNotifs(?User $user, SessionInterface $session)
    {
        if (!$user)
        {
            return;
        }
        $newLogs = $this->getPointsLogByUser($user->getId(),$user->getGroup() ? $user->getGroup()->getId() : null, $session);
        $newInvit = $this->getInvitationByUser($user->getId(),$session);
        $session->set('logs', array_map(function($log){return $log->getId();},$newLogs));
        $session->set('invit', array_map(function($invitation){return $invitation->getId();},$newInvit));    
    }

    private function getPointsLogByUser(?string $userId, ?string $groupId, SessionInterface $session) : array
    {
        if (!$userId) {
            return [];
        }
        $allPointsLog = $this->dm->getRepository(PointLog::class)->findAll();
        $pointsLogs = [];
        foreach($allPointsLog as $log)
        {
            if ($log->getUser()->getId() == $userId)
            {
                array_push($pointsLogs,$log);
            } else if ($log->getGroup()) {
                if ($log->getGroup()->getId() == $groupId) {
                    array_push($pointsLogs, $log);
            }}
        }
        $session->set('logs',array_map(function($log) {
            return $log->getId();
        },$pointsLogs));
        return $pointsLogs;
    }

    private function getInvitationByUser(?string $userId,SessionInterface $session):array
    {
        if (!$userId) {
            return [];
        }
        $allInvitations = $this->dm->getRepository(Invitation::class)->findAll();
        $invitations = [];
        foreach($allInvitations as $invit)
        {
            if ($invit->getReceiver()->getId() == $userId){
                array_push($invitations,$invit);
            }  
        }
        $session->set('invit',array_map(function($invit) {
            return $invit->getId();
        },$invitations));
        return $invitations;
    }
}
