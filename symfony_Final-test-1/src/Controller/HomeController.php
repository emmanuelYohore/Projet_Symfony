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
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(Request $request): Response
    {   
        $userRepository = $this->dm->getRepository(User::class);
        $users = $userRepository->findAll();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dm->persist($user);
            $this->dm->flush();
            return $this->redirectToRoute('home_index');
        }

        return $this->render('habitica/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/habitica-home/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, string $id): Response
    {
        $user = $this->dm->getRepository(User::class)->find($id);
        if ($user) {
            $this->dm->remove($user);
            $this->dm->flush();
        }

        return $this->redirectToRoute('home_index');
    }
}

