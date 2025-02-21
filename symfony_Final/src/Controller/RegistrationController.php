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
            // Hashage du mot de passe
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));

            // Sauvegarde de l'utilisateur dans la base de données
            $this->dm->persist($user);
            $this->dm->flush(); // On flush les changements (sauvegarde dans la base)

            // Message flash de succès
            $this->addFlash('success', 'Votre inscription a été réussie avec succès !');

            // Redirection vers la page d'accueil après l'inscription
            return $this->redirectToRoute('home_index');
        }

        // Si le formulaire n'est pas encore soumis ou valide
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }
}
