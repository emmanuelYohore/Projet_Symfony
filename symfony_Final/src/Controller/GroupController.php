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
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        if (!$session->get('connected_user'))
        {
            return $this->redirectToRoute('home_index');
        }
        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        if (!$user->getGroupId()) 
        {
            return $this->redirectToRoute('create_group');
        } else {
            return $this->redirectToRoute('add_to_group');
        }
    }

    #[Route("/create_group", name: "create_group")]
    public function create(Request $request, SessionInterface $session): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->dm->persist($group);
            $this->dm->flush();

            $emails = (array) $form->get('emails')->getData();
            foreach ($emails as $email)
            {
                $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user && !$user->getGroupId())
                {
                    $user->setGroupId($group->getId());
                    $this->dm->persist($user);
                }
                $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
                if ($user)
                {
                    $user->setGroupId($group->getId());
                    $this->dm->persist($user);
                }
            }

            $this->dm->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('group/createGroup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add_to_group', name: "add_to_group")]
    public function add(Request $request, SessionInterface $session): Response
    {

        $connected_user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        $group = $this->dm->getRepository(Group::class)->findOneBy(['id' => $connected_user->getGroupId()]);

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $emails = (array) $form->get('emails')->getData();

            foreach($emails as $email) {
                $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user)
                {
                    $user->setGroupId($group->getId());
                    $this->dm->persist($user);
                    $this->createInvitation($connected_user,$user,$group);
                }
            }

        }
        
        $this->dm->flush();
        $groupUser = $this->getUserByGroup($group->getId());
        return $this->render('group/addToGroup.html.twig', [
            'form' => $form->createView(),
            'group' => $group,
            'groupUser' => $groupUser
        ]);
    }

    public function getUserByGroup(?string $groupId): array
    {
        $users = $this->dm->getRepository(User::class)->findAll();
        $groupUser = [];
        foreach ($users as $user)
        {
            if ($user->getGroupId() == $groupId)
            {
                array_push($groupUser,$user);
            }
        }
        return $groupUser;
    }

    public function createInvitation(User $sender,User $receiver,Group $group)
    {
        $invitation = new Invitation();
        $invitation->setGroup($group->getId());
        $invitation->setSender($sender->getId());
        $invitation->setReceiver($receiver->getId());
        $this->dm->persist($invitation);
        $this->dm->flush();
    }
}