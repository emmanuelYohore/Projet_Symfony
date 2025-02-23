<?php

namespace App\Controller;

use App\Document\User;
use App\Document\PointLog;
use App\Document\Invitation;
use App\Form\ChangeProfilePictureType;
use App\Controller\HomeController;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/user/profile', name: 'user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, SessionInterface $session): Response
    {   
        $userId = $session->get('connected_user');
        $connected = false;
        $control = new HomeController($this->dm);
        $control->getNewNotifs($this->dm->getRepository(User::class)->find($userId),$session);
        $logs = $this->dm->getRepository(PointLog::class)->findBy(['id' => ['$in' => $session->get('logs') ? $session->get('logs') : []]]);
        $invits = $this->dm->getRepository(Invitation::class)->findBy(['id' => ['$in' => $session->get('invit')? $session->get('invit') : []]]);
        $notifs = $control->getOrderedNotifs($logs,$invits,$this->dm->getRepository(User::class)->find($userId),$session);
    
        if ($userId) {
            $connected = true;
        }

        if (!$userId) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->dm->getRepository(User::class)->find($userId);

        $form = $this->createForm(ChangeProfilePictureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();

            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$profilePicture->guessExtension();

                try {
                    $profilePicture->move(
                        $this->getParameter('picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading the image');
                    return $this->redirectToRoute('user_profile');
                }

                $user->setProfilePicture($newFilename);
                $this->dm->flush();

                $this->addFlash('success', 'Profile picture updated successfully');
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'connected' => $connected,
            'logs' => $logs,
            'invitations' => $invits,
            'allNotifs' => $notifs,
            'changeProfilePictureForm' => $form->createView(),
        ]);
    }
}
