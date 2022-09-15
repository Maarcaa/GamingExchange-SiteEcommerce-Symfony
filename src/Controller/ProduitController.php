<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ProduitFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/admin")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/voir-les-produits", name="show_produit", methods={"GET"})
     */
    public function showProduit(ArticleRepository $ArticleRepository, CategorieRepository $CategorieRepository): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        return $this->render("admin_produit/show_produit.html.twig", [
            'articles' => $ArticleRepository->findBy(['deletedAt' => null]),
            'categories' => $CategorieRepository->findBy(['deletedAt' => null]),
        ]);
    }

    /**
     * @Route("voir_produit_{id}", name="show_produit_{id}", methods={"GET"})
     */
    public function showProduitId(Article $article): Response
    {
        return $this->render("admin_produit/show_produit_id.html.twig", [
            'article' => $article
        ]);
    }

    /**
     * @Route("/creer-un-produit", name="create_produit", methods={"GET|POST"})
     */
    public function createProduit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $produit = new Article();

        $form = $this->createForm(ProduitFormType::class, $produit)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $produit->setCreatedAt(new DateTimeImmutable());
            $produit->setUpdatedAt(new DateTime());

            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            if($image) {
                // Méthode créée par nous-même pour réutiliser cette partie de code
                $this->handleFile($produit, $image, $slugger);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouveau produit est en ligne avec succès !');
            return $this->redirectToRoute('show_produit');
        }// end if($form)

        return $this->render('admin_produit/create_produit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-un-produit_{id}", name="update_produit", methods={"GET|POST"})
     */
    public function updateProduit(Article $article, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $originalimage = $article->getImage();

        $form = $this->createForm(ProduitFormType::class, $article, [
            'image' => $originalimage
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $article->setUpdatedAt(new DateTime());

            $image = $form->get('image')->getData();

            if($image) {
                // Méthode créée par nous-même pour réutiliser cette partie de code
                $this->handleFile($article, $image, $slugger);
            }
            else {
                $article->setImage($originalimage);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez modifié le produit avec succès !');
            return $this->redirectToRoute('show_produit');
        }

        return $this->render('admin_produit/update_produit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }



    ///////////////////////////////////////////////////////////////// PRIVATE FUNCTION /////////////////////////////////////////////////////////////

    /**
     * @param Produit $produit
     * @param UploadedFile $image
     * @param SluggerInterface $slugger
     * @return void
     */
    private function handleFile(Article $article, UploadedFile $image, SluggerInterface $slugger): void
    {
        
        # guessExtension() devine l'extension du fichier À PARTIR du MimeType du fichier
        #   => rappel : NE PAS confondre extension ET MimeType !
        $extension = '.' . $image->guessExtension();

        $safeFilename = $slugger->slug($article->getTitre());

        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        try {
            $image->move($this->getParameter('uploads_dir'), $newFilename);
            $article->setImage($newFilename);
        } catch (FileException $exception) {
            $this->addFlash('warning', 'La image du produit ne s\'est pas importée avec succès. Veuillez réessayer en modifiant le produit.');
        } // end catch()
    }

/**
     * @Route("/supprimer-un-produit/{id}", name="hard_delete_produit", methods={"GET"})
     */
    public function hardDeleteProduit(Article $article, EntityManagerInterface $entityManager): RedirectResponse
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }
        
        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé définitivement de la base');
        return $this->redirectToRoute('show_produit');
    }

}