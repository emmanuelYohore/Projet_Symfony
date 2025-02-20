<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($session->get('connected_user')) {
            return $this->redirectToRoute('home_index');
        }

        // Récupère les erreurs de connexion (si elles existent)
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $user = $this->dm->getRepository(User::class)->findOneBy(['username' => $username]);

            if ($user && password_verify($password, $user->getPassword())) {
                $session->set('connected_user', $user->getId());
                return $this->redirectToRoute('home_index');
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        }

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('connected_user');
        return $this->redirectToRoute('app_login');
    }
}
