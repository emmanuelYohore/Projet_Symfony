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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
    public function index(Request $request,SessionInterface $session): Response
    {   
        $userRepository = $this->dm->getRepository(User::class);
        $users = $userRepository->findAll();
        $id = $session->get('connected_user');
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get("profile_picture")->getData();

            if ($profilePicture) {
                $originalFileName = pathinfo($profilePicture->getClientOriginalName(),PATHINFO_FILENAME);
                $newFileName = $originalFileName . '-' . uniqid() . '.' . $profilePicture->guessExtension();
                try {
                    $profilePicture->move(
                        $this->getParameter('picture_directory'), // Assure-toi d’avoir ce paramètre configuré dans services.yaml
                        $newFileName
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_register');
                }
                
                $user->setProfilePicture($newFileName);
            }
            $user->setPassword(password_hash($user->getPassword(),PASSWORD_BCRYPT));
            $this->dm->persist($user);
            $session->set('connected_user',$user->getId());
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