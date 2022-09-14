<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Categorie;
use App\Form\CategorieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/admin")
 */
class CategorieController extends AbstractController
{
     /**
     * @Route("/voir-categorie", name="show_categorie", methods={"GET"})
     */
    public function showCategorie(EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux administrateurs');
            return $this->redirectToRoute('default_home');
        }

        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        return $this->render('admin_categorie/show_categorie.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("voir_categorie_{id}", name="show_categorie_{id}", methods={"GET"})
     */
    public function showSliderId(Categorie $categorie): Response
    {
        return $this->render("admin_categorie/show_categorie_id.html.twig", [
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/ajouter-une-categorie", name="create_categorie", methods={"GET|POST"})
     */
    public function createcategorie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieFormType::class, $categorie)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $categorie->setCreatedAt(new DateTimeImmutable());
            $categorie->setUpdatedAt(new DateTime());

            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', "La catégorie a bien été ajouté");
            return $this->redirectToRoute('show_categorie');
        }

        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        return $this->render("admin_categorie/create_categorie.html.twig", [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/modifier-une-categorie/{id}", name="update_categorie", methods={"GET|POST"})
     */
    public function updatecategorie(categorie $categorie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(categorieFormType::class, $categorie)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $categorie->setUpdatedAt(new DateTime());

            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', "La catégorie a bien été modifié");
            return $this->redirectToRoute('show_categorie');
        }

        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        return $this->render("admin_categorie/update_categorie.html.twig", [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/supprimer-une-categorie/{id}", name="hard_delete_categorie", methods={"GET"})
     */
    public function hardDeletecategorie(categorie $categorie, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($categorie);
        $entityManager->flush();

        $this->addFlash('success', 'La catégorie a bien été supprimé définitivement de la base');
        return $this->redirectToRoute('show_categorie');
    }

} # end class
