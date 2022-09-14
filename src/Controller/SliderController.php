<?php

namespace App\Controller;

use DateTime;
use App\Entity\Slider;
use DateTimeImmutable;
use App\Form\SliderFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SliderController extends AbstractController
{
    /**
     * @Route("/slider", name="show_slider", methods={"GET|POST"})
     */
    public function showSlider(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }


        $sliders = $entityManager->getRepository(Slider::class)->findAll();
        return $this->render("admin_slider/show_slider.html.twig", [
            'sliders' => $sliders
        ]);
    }

    /**
     * @Route("/ajouter-un-slider", name="create_slide", methods={"GET|POST"})
     */
    public function createSlider(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $slider = new Slider();

        $form = $this->createForm(SliderFormType::class, $slider)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $slider->setCreatedAt(new DateTimeImmutable());
            $slider->setUpdatedAt(new DateTime());

                /** @var UploadedFile $photo */
                $photo = $form->get('photo')->getData();

                if ($photo) {
                    $extension = '.' . $photo->guessExtension();
                    $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
    
    
                    $newFilename = $safeFilename . '_' . uniqid() . $extension;
    
                    try {
                        $photo->move($this->getParameter('uploads_dir'), $newFilename);
                        $slider->setPhoto($newFilename);
                        $slider->setOrdre($form->get('ordre')->getData());
                    } catch (FileException $exception) {
                        $this->addFlash('erreur', 'Votre photo n\'a pas été téléchargé');
                    }
                } #end if photo

            $entityManager->persist($slider);
            $entityManager->flush();

            $this->addFlash('success', "Le slider a bien été ajouté");
            return $this->redirectToRoute('show_slider');
        }

        $sliders = $entityManager->getRepository(Slider::class)->findAll();
        return $this->render("admin_slider/create_slider.html.twig", [
            'form' => $form->createView(),
            'sliders' => $sliders,
        ]);
    }

    /**
     * @Route("voir_slider_{id}", name="show_slider_{id}", methods={"GET"})
     */
    public function showSliderId(Slider $slider): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        return $this->render("admin_slider/show_slider_id.html.twig", [
            'slider' => $slider
        ]);
    }

    /**
     * @Route("/modifier-slider_{id}", name="update_slider", methods={"GET|POST"})
     */
    public function updateSlider(Slider $slider, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $form = $this->createForm(SliderFormType::class, $slider)->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $slider->setUpdatedAt(new DateTime());


            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $extension = '.' . $photo->guessExtension();
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);


                $newFilename = $safeFilename . '_' . uniqid() . $extension;

                try {
                    $photo->move($this->getParameter('uploads_dir'), $newFilename);
                    $slider->setPhoto($newFilename);
                    $slider->setOrdre($form->get('ordre')->getData());
                } catch (FileException $exception) {
                    $this->addFlash('erreur', 'Votre photo n\'a pas été uploader');
                }
            } #end if photo

        }
        
        $entityManager->persist($slider);
        $entityManager->flush();

        $sliders = $entityManager->getRepository(Slider::class)->findAll();

        return $this->render("admin_slider/update_slider_id.html.twig", [
            'form' => $form->createView(),
            'sliders' => $sliders,
            'slider' => $slider
        ]); 
        
        $this->addFlash('success', "Le slider a bien été modifié de la base de données");
        return $this->redirectToRoute('show_slider');
    } # end function updateSlider




    /**
     * @Route("/supprimer-slider_{id}", name="hard_delete_slider", methods={"GET"})
     */
    public function hardDeleteSlider(Slider $slider, EntityManagerInterface $entityManager): RedirectResponse
    {
        try {
            $this->denyAccessUnLessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $entityManager->remove($slider);
        $entityManager->flush();

        $this->addFlash('success', "Le slider a bien été supprimé de la base de données");
        return $this->redirectToRoute('show_slider');
    }
}
