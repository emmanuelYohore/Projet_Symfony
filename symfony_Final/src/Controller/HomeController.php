<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController {
   
    #[Route('/products', name: 'products_list')]
    public function showproducts()
    {
       
        $products = ['ordinateur','telephone','radio','cassette'];
        return $this->render('product.html.twig', ['products' => $products]);
    }

}