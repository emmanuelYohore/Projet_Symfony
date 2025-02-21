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
            
            $this->dm->persist($user);
            $session->set('connected_user',$user->getId());
            $this->dm->flush();
            return $this->redirectToRoute('home_index');
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
