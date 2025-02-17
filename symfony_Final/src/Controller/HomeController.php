<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController {
    public function bonjour()
    {
        return new Response("Hello word");
    }

    public function aurevoir()
    {
        return $this->redirectToRoute('acceuil');
    }

    public function showtemplate()
    {
        return $this->render('base.html.twig',[]);
    }
}