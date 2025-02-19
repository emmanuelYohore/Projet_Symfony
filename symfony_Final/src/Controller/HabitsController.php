<?php

namespace App\Controller;

use App\Form\HabitsType;
use App\Entity\Habits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class HabitsController extends AbstractController
{
    #[Route('/formhabits', name: 'formhabits')]
    public function index(Request $request, ManagerRegistry $doctrine)
    {
        $habits = new Habits();

        $formhabits = $this->createForm(HabitsType::class, $habits);

        $formhabits->handleRequest($request);

        if($formhabits->isSubmitted() && $formhabits->isValid())
        {
            $entitymanager = $doctrine->getManager();
            $habits = $formhabits->getData();
            //insertion des données dans la bdd
            $entitymanager->persist($habits);
            $entitymanager->flush();
        }
        return $this->render('habits/index.html.twig', [
            'formhabits' => $formhabits->createView()
        ]);
    }
}
