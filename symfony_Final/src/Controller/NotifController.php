<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Document\Invitation;
use App\Document\User;
use App\Document\PointLog;
use App\Document\Group;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;

final class NotifController extends AbstractController
{
    private DocumentManager $dm;
    private LoggerInterface $logger;
    public function __construct(DocumentManager $dm, LoggerInterface $logger)
    {
        $this->dm = $dm;
        $this->logger = $logger;
    }

    #[Route('/notif', name: 'app_notif')]
    public function index(Request $request,SessionInterface $session): Response
    {
        if (!$session->get('connected_user'))
        {
            return $this->redirectToRoute('home_index');
        }
        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $session->get('connected_user')]);
        $invitations = $this->getInvitationByUser($user->getId());
        $pointsLogs = $this->getPointsLogByUser($user->getId(),$user->getGroup() ? $user->getGroup()->getId() : null);



        return $this->render('notif/index.html.twig', [
            'logs' => $pointsLogs,
            'invitations' => $invitations,
            'controller_name' => 'NotifController',
            'allNotifs' => $this->getOrderedNotifs($pointsLogs,$invitations)
        ]);
    }

    private function getInvitationByUser(?string $userId):array
    {
        $allInvitations = $this->dm->getRepository(Invitation::class)->findAll();
        $invitations = [];
        foreach($allInvitations as $invit)
        {
            array_push($invitations,$invit);
            // if ($invit->getReceiver()->getId() == $userId){
            //     array_push($invitations,$invit);
            // }
                
        }
        return $invitations;
    }

    private function getPointsLogByUser(?string $userId, ?string $groupId = "a"):array
    {
        $allPointsLog = $this->dm->getRepository(PointLog::class)->findAll();
        $pointsLogs = [];
        foreach($allPointsLog as $log)
        {
            array_push($pointsLogs,$log);
            // if ($log->getUser()->getId() == $userId || $log->getGroup() ? $log->getGroup()->getId() == $groupId : false)
            // {
            //     array_push($pointsLogs,$log);
            // }
        }
        return $pointsLogs;
    }

    private function getOrderedNotifs(array $logs, array $invitations) : array
    {
        $allNotifs = array_merge($logs, $invitations);
        usort($allNotifs, function ($a, $b) {
            return $b->getTimestamp() <=> $a->getTimestamp();
        });
        return $allNotifs;
    }

    #[Route('/accept/{groupId}/{invitId}', name: "accept_invit", methods: ['POST'])]
    public function acceptInvitation(Request $request, SessionInterface $session, string $groupId, string $invitId) : Response
    {
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
        return $this->redirectToRoute('app_notif');
    }

    #[Route('/decline/{groupId}/{invitId}', name: "decline_invit", methods: ['POST'])]
    public function declineInvitation(Request $request, SessionInterface $session, string $groupId, string $invitId) : Response
    {
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
        return $this->redirectToRoute('app_notif');
    }

    private function removeInvitation(string $invitation)
    {
        $invit = $this->dm->getRepository(Invitation::class)->find($invitation);
        $this->dm->remove($invit);
    }
}
