<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RenderController extends AbstractController
{
    /**
     * @Route("/categories", name="render_categories_in_nav")
     */
    public function renderCategoriesInNav(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Categorie::class)->findBy(['deletedAt' => null]);

        return $this->render('rendered/categories_in_nav.html.twig', [
            'categories' => $categories
        ]);
    }

     /**
     * @Route("/voir-article/{marque}", name="show_articles_from_categorie", methods={"GET"})
     */
    public function showArticlesFromCategory(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy(['categorie' => $categorie->getId(),'deletedAt' => null ]);
          

        return $this->render("rendered/show_articles_from_categorie.html.twig", [
            'articles' => $articles,
            'categorie' => $categorie
        ]);
    }
}
