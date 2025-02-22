<?php

namespace App\Controller;

use App\Document\User; // Ajout de l'import de User
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
        $form = $formFactory->create(LoginType::class);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $identifier = $data['identifier'];
            $password = $data['password'];

            if (str_contains($identifier,'@')) { $user = $this->dm->getRepository(User::class)->findOneBy(['email' => $identifier]); }
            else {$user = $this->dm->getRepository(User::class)->findOneBy(['username' => $identifier]);}

            if ($user && password_verify($password, $user->getPassword())) {
                $session->set('connected_user', $user->getId());
                return $this->redirectToRoute('user_profile');
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        }

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('logs');
        $session->remove('invit');
        $session->remove('connected_user');
        return $this->redirectToRoute('home_index');
    }
}
