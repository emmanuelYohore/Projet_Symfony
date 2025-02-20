<?php

namespace App\Controller;

use App\Document\User;
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

        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->dm->getRepository(User::class)->find($userId);


        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }
}