<?php

namespace App\Controller;

use App\Document\User;
use App\Document\PointLog;
use App\Document\Invitation;
use App\Controller\HomeController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/user/profile', name: 'user_profile', methods: ['GET'])]
    public function profile(SessionInterface $session): Response
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

        $userId = $session->get('connected_user');

        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->dm->getRepository(User::class)->find($userId);


        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'connected' => $connected,
            'logs' => $logs,
            'invitations' => $invits,
            'allNotifs' => $notifs,
        ]);
    }
}
