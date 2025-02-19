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
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GroupController extends AbstractController
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


        return $this->render('group/index.html.twig', [
            'name' => $session->get('connected_user'),
            'controller_name' => 'GroupController',
        ]);
    }
}
