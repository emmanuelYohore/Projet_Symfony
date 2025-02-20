<?php

namespace App\Controller;

use App\Document\Habit;
use App\Document\User;
use App\Document\Group;
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

    #[Route('/home', name: 'home_index')]
    public function index(SessionInterface $session): Response
    {
        $userId = $session->get('connected_user');
        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->dm->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user) {
            return $this->redirectToRoute('app_logout');
        }

        $userHabits = $this->dm->getRepository(Habit::class)->findBy(['user' => $user]);

      
        $groupHabits = [];
        if ($user->getGroupId()) {
            $group = $this->dm->getRepository(Group::class)->findOneBy(['id' => $user->getGroupId()]);
            if ($group) {
                $groupHabits = $this->dm->getRepository(Habit::class)->findBy(['group' => $group]);
            }
        }

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'userHabits' => $userHabits,
            'groupHabits' => $groupHabits
        ]);
    }
}
