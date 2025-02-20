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
        ]);
    }

    private function getInvitationByUser(?string $userId):array
    {
        $allInvitations = $this->dm->getRepository(Invitation::class)->findAll();
        $invitations = [];
        foreach($allInvitations as $invit)
        {
            array_push($invitations,$invit);
            if ($invit->getReceiver()->getId() == $userId)
                array_push($invitations,$invit);
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
            if ($log->getUser()->getId() == $userId || $log->getGroup()->getId() == $groupId)
            {
                array_push($pointsLogs,$log);
            }
        }
        return $pointsLogs;
    }
}
