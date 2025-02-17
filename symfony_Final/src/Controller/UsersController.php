<?php

namespace App\Controller;

use App\Form\UsersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UsersController extends AbstractController
{

    //chemin pour le formulaire
    #[Route('/formusers', name: 'formusers')]
    public function index(Request $request, ManagerRegistry $doctrine)
    {
        $users = new Users();
        $formusers = $this->createForm(UsersType::class, $users);

        $formusers->handleRequest($request);

        if($formusers->isSubmitted() && $formusers->isValid())
        {
            $entitymanager = $doctrine->getManager();
            $user = $formusers->getData();

            //insertion des données dans la bdd
            $entitymanager->persist($user);
            $entitymanager->flush();

        }

        return $this->render('users/index.html.twig', [
            'formusers' => $formusers->createView()
        ]);
    }
}
