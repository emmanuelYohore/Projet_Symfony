<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Document\User; // Ajout de l'import de User
=======
use App\Document\User;
>>>>>>> origin/emmanuel
use App\Form\LoginType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, SessionInterface $session, FormFactoryInterface $formFactory): Response
    {
<<<<<<< HEAD

=======
       
>>>>>>> origin/emmanuel
        $form = $formFactory->create(LoginType::class);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
<<<<<<< HEAD
            $username = $data['username'];
            $password = $data['password'];

            $user = $this->dm->getRepository(User::class)->findOneBy(['username' => $username]);

=======
            $identifier = $data['identifier']; 
            $password = $data['password'];

           
            $user = $this->dm->getRepository(User::class)->findOneBy(['username' => $identifier]);

            if (!$user) {
                $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $identifier]);
            }

          
>>>>>>> origin/emmanuel
            if ($user && password_verify($password, $user->getPassword())) {
                $session->set('connected_user', $user->getId());
                return $this->redirectToRoute('user_profile');
            } else {
<<<<<<< HEAD
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
=======
                $error = 'Nom d\'utilisateur/e-mail ou mot de passe incorrect.';
>>>>>>> origin/emmanuel
            }
        }

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/emmanuel
