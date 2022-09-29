<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Paiement;
use App\Form\PaiementFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="paiement_add")
     */
    public function Paiement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $this->getUser(), 'archive' => 'non'], ['updatedAt' => 'DESC']);
        if ($panier == null) {
            $panier = $entityManager->getRepository(Panier::class)->findOneBy(['session' => $request->getSession()->get('session')]);
        }
        $numberOfItem = null;
        $panierProduit = null;
        if ($panier !== null) {
            $numberOfItem = $panier->getPanierProduit()->count();
        }
        if ($panier !== null) {
            $panierProduit = $panier->getPanierProduit()->toArray();
        }

        $total = 0;
        if ($panierProduit !== null) {
            foreach ($panierProduit as $item) {

                $totalItem = $item->getPrix() * $item->getQuantite();


                $total += $totalItem; # => $total = $total + $totalItem
            }
        }
        
        $paiement = new Paiement;
        $user = $entityManager->getRepository(User::class)->findAll();

        foreach($user as $item){ 
        $paiement->setPrenom($item->getPrenom());
        $paiement->setNom($item->getNom());
        }
        $form = $this->createForm(PaiementFormType::class, $paiement)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paiement->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($paiement);
            $entityManager->flush();

            $this->addFlash('success', " Votre paiement a été confirmé");
            return $this->redirectToRoute('default_home');
        }

        return $this->render('paiement/show_paiement.html.twig', [
            'form' => $form->createView(),
            'total' => $total,
            'panierproduit' => $panierProduit
        ]);
    }
}