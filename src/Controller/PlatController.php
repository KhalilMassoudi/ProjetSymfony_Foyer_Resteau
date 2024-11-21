<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use App\Repository\CategoriePlatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlatController extends AbstractController
{
    #[Route('/plat', name: 'app_plat_index')]
    public function index(PlatRepository $platRepository): Response
    {
        return $this->render('plat/index.html.twig', [
            'plats' => $platRepository->findAll(),
        ]);
    }

    #[Route('/plat/new', name: 'app_plat_new')]
    public function new(Request $request, CategoriePlatRepository $categoriePlatRepository): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_index');
        }

        return $this->render('plat/new.html.twig', [
            'form' => $form->createView(),
            'categories' => $categoriePlatRepository->findAll(),
        ]);
    }

    #[Route('/plat/{id}', name: 'app_plat_show')]
    public function show(Plat $plat): Response
    {
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
        ]);
    }

    #[Route('/plat/{id}/edit', name: 'app_plat_edit')]
    public function edit(Request $request, Plat $plat): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_plat_index');
        }

        return $this->render('plat/edit.html.twig', [
            'form' => $form->createView(),
            'plat' => $plat,
        ]);
    }

    #[Route('/plat/{id}/delete', name: 'app_plat_delete')]
    public function delete(Request $request, Plat $plat): Response
    {
        if ($this->isCsrfTokenValid('delete' . $plat->getId(), $request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plat_index');
    }
}
