<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Article;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class PanierController extends AbstractController
{
    /**
     * @Route("/voir-mon-panier", name="show_panier", methods={"GET"})
     * @param SessionInterface $session
     * @return Response
     */
    public function showPanier(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $total = 0;

        foreach($panier as $item){
                $totalItem = $item['article']->getPrix() * $item['quantity'];
                $total += $totalItem; # => $total = $total + $totalItem
            }

        return $this->render("panier/show_panier.html.twig", [
            'total' => $total
        ]);
    }

    /**
     * @Route("/ajouter-un-article/{id}", name="panier_add", methods={"GET"})
     * @param Article $article
     * @param SessionInterface $session
     * @return Response
     */
    public function add(Article $article, SessionInterface $session): Response
    {
        // Si dans la session le panier n'existe pas, alors la méthode get() retournera le second paramètre, un array vide.
        $panier = $session->get('panier', []);

        if( !empty( $panier[$article->getId()] ) ) {
            ++$panier[$article->getId()]['quantity'];
        } else {
            $panier[$article->getId()]['quantity'] = 1;
            $panier[$article->getId()]['article'] = $article;
        }

        // Ici, nous devons set() le panier en session, en lui passant notre $panier[]
        $session->set('panier', $panier);

        $this->addFlash('success', "L'article a été ajouté à votre panier");
        return $this->redirectToRoute('default_home');
    }

    /**
     * @Route("/vider-mon-panier", name="empty_panier", methods={"GET"})
     * @return Response
     */
    public function emptyPanier(SessionInterface $session): Response
    {
        // La méthode remove() permet de supprimer un attribut de la $session.
        $session->remove('panier');

        return $this->redirectToRoute('show_panier');
    }

    /**
     * @Route("/retirer-du-panier/{id}", name="panier_remove", methods={"GET"})
     * @param int $id
     * @param SessionInterface $session
     * @return Response
     */
    public function delete(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier');

        // array_key_exists() est une fonction native de PHP, qui permet de vérifier si une key existe dans un array.
            # cette fonction prend 2 arguments = la valeur à vérifier, le tableau dans lequel rechercher.
        if(array_key_exists($id, $panier)) {
            // unset() est une fonction native de PHP, qui permet de supprimer une variable (ou une key dans un array)
            unset($panier[$id]);
        } else {
            $this->addFlash('warning', "Ce article n'est pas dans votre panier.");
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute("show_panier");
    }

    /**
     * @Route("/valider-mon-panier", name="panier_validate", methods={"GET"})
     */
    public function validateCommande(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $panier = $session->get('panier', []);

        if(empty($panier)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('show_panier');
        }

        $commande = new Commande();
//        $user =

        $commande->setCreatedAt(new DateTimeImmutable());
        $commande->setUpdatedAt(new DateTime());

        $total = 0;


        foreach($panier as $item) {
            $totalItem = $item['article']->getPrix() * $item['quantity'];
            $total += $totalItem;

            $commande->setQuantite($item['quantite']);
        }



        $commande->setEtat('en préparation');
        $commande->setUser($this->getUser());
        $commande->setMontantCommande($total);

        $entityManager->persist($commande);
        $entityManager->flush();

        $session->remove('panier');

        $this->addFlash('success', "Bravo, votre commande est prise en compte et en préparation. Vous pouvez la retrouver dans Mes Commandes.");
        return $this->redirectToRoute('show_profile');

    }// end function validate()
}
