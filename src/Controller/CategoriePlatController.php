<?php

namespace App\Controller;

use App\Entity\CategoriePlat;
use App\Form\CategoriePlatType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoriePlatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriePlatController extends AbstractController
{
    #[Route('/categorie/plat', name: 'app_categorie_plat_index')]
    public function index(CategoriePlatRepository $categoriePlatRepository): Response
    {
        return $this->render('categorie_plat/index.html.twig', [
            'categories' => $categoriePlatRepository->findAll(),
        ]);
    }

    #[Route('/categorie/plat/new', name: 'app_categorie_plat_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new CategoriePlat object
        $categoriePlat = new CategoriePlat();

        // Create the form based on the CategoriePlatType
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);

        // Handle the request
        $form->handleRequest($request);

        // If the form is submitted and valid, save the category and redirect
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoriePlat);
            $entityManager->flush();

            // Redirect to the list of categories (you can modify this route)
            return $this->redirectToRoute('app_categorie_plat_index');
        }

        // Render the form in the view
        return $this->render('categorie_plat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/categorie/plat/{id}', name: 'app_categorie_plat_show')]
    public function show(CategoriePlat $categoriePlat): Response
    {
        return $this->render('categorie_plat/show.html.twig', [
            'categorie' => $categoriePlat,
        ]);
    }

    #[Route('/categorie/plat/{id}/edit', name: 'app_categorie_plat_edit')]
    public function edit(Request $request, CategoriePlat $categoriePlat): Response
    {
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_categorie_plat_index');
        }

        return $this->render('categorie_plat/edit.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categoriePlat,
        ]);
    }

    #[Route('/categorie/plat/{id}/delete', name: 'app_categorie_plat_delete')]
    public function delete(Request $request, CategoriePlat $categoriePlat): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categoriePlat->getId(), $request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categoriePlat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_plat_index');
    }
}
