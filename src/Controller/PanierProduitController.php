<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierProduitController extends AbstractController
{
    /**
     * @Route("/panier/produit", name="app_panier_produit")
     */
    public function index(): Response
    {
        return $this->render('panier_produit/index.html.twig', [
            'controller_name' => 'PanierProduitController',
        ]);
    }
}
