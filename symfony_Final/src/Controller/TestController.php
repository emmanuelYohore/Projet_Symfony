<?php

namespace App\Controller;

use App\Document\Test;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/create-test', name: 'create_test')]
    public function createTest(Request $request, DocumentManager $dm): Response
    {
        // Si le formulaire est soumis et valide
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $age = $request->request->get('age');

            // Vérification que les données sont présentes
            if (empty($nom) || empty($age)) {
                return new Response('Le nom et l\'âge sont requis.', Response::HTTP_BAD_REQUEST);
            }

            // Crée un nouvel objet Test
            $test = new Test($nom, (int)$age);
            // Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $age = $request->request->get('age');

            // Vérification que les données sont présentes
            if (empty($nom) || empty($age)) {
                return new Response('Le nom et l\'âge sont requis.', Response::HTTP_BAD_REQUEST);
            }

            // Assigner les données à l'objet Test
            $test->setNom($nom);
            $test->setAge((int)$age);  // Assurer que $age est un entier

            // Enregistrer dans MongoDB
            $dm->persist($test);
            $dm->flush();

            return new Response('Données enregistrées avec succès!');
        }

        // Afficher le formulaire
        return $this->render('test/create.html.twig');
    }
}
