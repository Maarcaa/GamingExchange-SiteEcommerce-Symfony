<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Panier;
use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\PanierProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PanierController extends AbstractController
{
    /**
     * @Route("/voir-mon-panier", name="show_panier", methods={"GET"})
     * @param SessionInterface $session
     * @return Response
     */
    public function showPanier(EntityManagerInterface $entityManager, Request $request): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $this->getUser(), 'archive' => 'non' ], ['updatedAt' => 'DESC']);
        if ($panier == null) {
            $panier = $entityManager->getRepository(Panier::class)->findOneBy(['session' => $request->getSession()->get('session')]);
        }
        // dd($panier);
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
        return $this->render("panier/show_panier.html.twig", [
            'total' => $total,
            'panierproduit' => $panierProduit
        ]);
    }

    /**
     * @Route("/ajouter-un-article/{id}", name="panier_add", methods={"GET"})
     * @param Article $article
     * @param SessionInterface $session
     * @return Response
     */
    public function add(Article $article, EntityManagerInterface $entityManager, Request $request): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['session' => $request->getSession()->get('session')]);

        if ($this->getUser() !== null && $panier == null) {
            $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $this->getUser(), 'archive' => 'non' ], ['updatedAt' => 'DESC']);
        }

        if ($panier == null) {
            $panier = new Panier();
            $request->getSession()->set('session', uniqid(rand(), true));
        }

        $panier->setCreatedAt(new DateTimeImmutable());
        $panier->setUpdatedAt(new DateTime());
        $panier->setUser($this->getUser());
        $panier->setSession($request->getSession()->get('session'));
        $panier->setArchive('non');



        // $panier = $session->get('panier', []);

        // if( !empty( $panier->getId())) {
        //     ++$panier->getId()['quantity'];
        // } else {
        //     $panier->getId()['quantity'] = 1;
        //     $panier[$article->getId()]['article'] = $article;
        // }

        $panierProduit = new PanierProduit();

        $panierProduit->setCreatedAt(new DateTimeImmutable());
        $panierProduit->setUpdatedAt(new DateTime());
        $panierProduit->setPhoto($article->getImage());
        $panierProduit->setTitre($article->getTitre());
        $panierProduit->setDescription($article->getDescription());
        $panierProduit->setPrix($article->getPrix());
        $panierProduit->setUser($this->getUser());
        $panierProduit->setQuantite(1);
        $panier->addPanierProduit($panierProduit);

        $entityManager->persist($panierProduit);
        $entityManager->persist($panier);

        $entityManager->flush();

        $this->addFlash('success', "L'article a été ajouté à votre panier");
        return $this->redirectToRoute('show_panier');
    }

    /**
     * @Route("/ajouter_un_article_panier_produit_{id}", name="panier_add_product", methods={"GET|POST"})
     */
    public function panierAddProduct(int $id, EntityManagerInterface $entityManager)
    {
        $panierProduit = $entityManager->getRepository(PanierProduit::class)->findOneBy(['id' => $id]);
        $panierProduit->setQuantite($panierProduit->getQuantite() + 1);


        $entityManager->persist($panierProduit);
        $entityManager->flush();

        return $this->redirectToRoute('show_panier');
    }
    /**
     * @Route("/enlever_un_article_panier_produit_{id}", name="panier_minus_product", methods={"GET|POST"})
     */
    public function panierMinusProduct(int $id, EntityManagerInterface $entityManager)
    {
        $panierProduit = $entityManager->getRepository(PanierProduit::class)->findOneBy(['id' => $id]);
        $panierProduit->setQuantite($panierProduit->getQuantite() - 1);


        $entityManager->persist($panierProduit);
        $entityManager->flush();

        return $this->redirectToRoute('show_panier');
    }

    /**
     * @Route("/vider-mon-panier", name="empty_panier", methods={"GET"})
     * @return Response
     */
    public function emptyPanier(EntityManagerInterface $entityManager, Request $request): Response
    {
        $panier = $entityManager->getRepository(Panier::class)->findOneBy(['user' => $this->getUser(), 'archive' => 'non' ], ['updatedAt' => 'DESC']);
        if ($panier == null) {
            $panier = $entityManager->getRepository(Panier::class)->findOneBy(['session' => $request->getSession()->get('session')]);
        }
        $panierProduit = $entityManager->getRepository(PanierProduit::class)->findBy(['panier' => $panier->getId()]);

        foreach ($panierProduit as $item) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('show_panier');
    }

    /**
     * @Route("/retirer-du-panier/{id}", name="panier_remove", methods={"GET"})
     * @param int $id
     * @param SessionInterface $session
     * @return Response
     */
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $panierProduit = $entityManager->getRepository(PanierProduit::class)->findOneBy(['id' => $id]);

        $entityManager->remove($panierProduit);
        $entityManager->flush();

        return $this->redirectToRoute("show_panier");
    }

    /**
     * @Route("/valider-mon-panier", name="panier_validate", methods={"GET"})
     */
    public function validatePanier(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('show_panier');
        }

        $panier = new Panier();

        $panier->setCreatedAt(new DateTimeImmutable());
        $panier->setUpdatedAt(new DateTime());

        $total = 0;

        $article = new Article();
        foreach ($panier as $item) {
            $totalItem = $article->getPrix() * $item['quantity'];
            $total += $totalItem;
        }

        $panier->setUser($this->getUser());


        $entityManager->persist($panier);
        $entityManager->flush();

        $session->remove('panier');

        $this->addFlash('success', "Bravo, votre commande est prise en compte et en préparation. Vous pouvez la retrouver dans Mes Commandes.");
        return $this->redirectToRoute('show_profile');
    } // end function validate()
}
