<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_home", methods={"GET"})
     * @return Response
     */
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy(['deletedAt' => null]);

        return $this->render('default/home.html.twig', [
            'articles' => $articles
                ]);
    }
}