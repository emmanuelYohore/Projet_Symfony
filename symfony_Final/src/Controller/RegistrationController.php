<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegistrationController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/registration', name: 'app_registration', methods: ['GET', 'POST'])]
    public function index(Request $request, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user); // Création du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            $profilePicture = $form->get("profile_picture")->getData();

            if ($profilePicture)
            {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(),PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();
                try {
                    $profilePicture->move(
                        $this->getParameter('picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                    return $this->redirectToRoute('app_registration');
                }

                $user->setProfilePicture($newFilename);
            }
            else
            {
                $user->setProfilePicture("default.png");
            }


            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));

            // Sauvegarde de l'utilisateur dans la base de données
            $this->dm->persist($user);
            $this->dm->flush(); // On flush les changements (sauvegarde dans la base)

            // Message flash de succès
            // Redirection vers la page d'accueil après l'inscription
            return $this->redirectToRoute('home_index');
        }

        // Si le formulaire n'est pas encore soumis ou valide
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }
}
