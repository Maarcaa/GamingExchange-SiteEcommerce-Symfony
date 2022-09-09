<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\InscriptionFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminMembreController extends AbstractController
{
    /**
     * @Route("/voir-membre", name="show_membre", methods={"GET"})
     */
    public function showMembre(EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux administrateurs');
            return $this->redirectToRoute('default_home');
        }

        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin_membre/show_membre.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/modifier-membre_{id}", name="update_membre", methods={"GET|POST"})
     */
    public function updateMembre(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(InscriptionFormType::class, $user)->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUpdatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', "Le membre a été modifié avec succès !");
            return $this->redirectToRoute('show_membre');
        }
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render("admin_membre/update_membre.html.twig", [
            'form' => $form->createView(),
            'users' => $users
        ]);
    }


    /**
     * @Route("/supprimer_membre_{id}", name="hard_delete_membre", methods={"GET"})
     */
    public function hardDeleteMembre(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('succès', 'Le membre a bien été supprimé');
        return $this->redirectToRoute('show_membre');

    }

}
