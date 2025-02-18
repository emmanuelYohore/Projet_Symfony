<?php

namespace App\Controller;

use App\Form\HabitType;
use App\Entity\Habit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

final class HabitController extends AbstractController
{
    #[Route('/formhabit', name: 'formhabit')]
    public function index(Request $request, ManagerRegistry $doctrine)
    {
        $habit = new Habit();

        $formhabit = $this->createForm(HabitType::class, $habit);

        $formhabit->handleRequest($request);

        if($formhabit->isSubmitted() && $formhabit->isValid())
        {
            $entitymanager = $doctrine->getManager();
            $habit = $formhabit->getData();
            //insertion des données dans la bdd
            $entitymanager->persist($habit);
            $entitymanager->flush();
        }
        return $this->render('habit/index.html.twig', [
            'formhabit' => $formhabit->createView()
        ]);
    }
}
