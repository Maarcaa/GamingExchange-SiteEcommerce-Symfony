<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Livraison;
use App\Form\DeliveryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeliveryController extends AbstractController
{
    /**
     * @Route("/delivery", name="register_delivery")
     */
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livraison = new Livraison;
        
        $user = $entityManager->getRepository(User::class)->findAll();

        foreach($user as $item){ 
        $livraison->setPrenom($item->getPrenom());
        $livraison->setNom($item->getNom());
        $livraison->setAdresse($item->getAdresse());
        $livraison->setCodePostal($item->getCodePostal());
        $livraison->setVille($item->getVille());
        $livraison->setTelephone($item->getTelephone());
        $livraison->setEmail($item->getEmail());
        }
        $form = $this->createForm(DeliveryFormType::class, $livraison)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraison->setCreatedAt(new DateTimeImmutable());
            $livraison->setUpdatedAt(new DateTime());

            $entityManager->persist($livraison);
            $entityManager->flush();

            $this->addFlash('success', " Vos coordonnées de livraison ont bien été confirmé");
            return $this->redirectToRoute('paiement_add');
        }

        return $this->render('delivery/register_delivery.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
