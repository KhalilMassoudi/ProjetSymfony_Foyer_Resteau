<?php

namespace App\Controller;

use App\Entity\CategoriePlat;
use App\Form\CategoriePlatType;
use App\Repository\CategoriePlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

class CategoriePlatController extends AbstractController
{
    #[Route("/categorieplat", name: "app_categorieplat")]
    public function index(Request $request, EntityManagerInterface $entityManager, CategoriePlatRepository $categoriePlatRepository): Response
    {
        $categoriePlat = new CategoriePlat();
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoriePlat);
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie ajoutée avec succès !');
            return $this->redirectToRoute('app_categorieplat');
        }

        return $this->render('backtemplates/app_categorieplat.html.twig', [
            'form' => $form->createView(),
            'categories' => $categoriePlatRepository->findAll(),
        ]);
    }

    #[Route("/categorieplat/edit/{id}", name: "app_categorieplat_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, CategoriePlatRepository $categoriePlatRepository): Response
    {
        // Retrieve the category by ID
        $categoriePlat = $categoriePlatRepository->find($id);

        // Check if the category exists
        if (!$categoriePlat) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        // Create the form for editing the category
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the changes to the category in the database
            $entityManager->flush();

            // Add a success flash message
            $this->addFlash('success', 'Catégorie modifiée avec succès !');

            // Redirect to the category list page
            return $this->redirectToRoute('app_categorieplat');
        }

        return $this->render('backtemplates/app_edit_categorieplat.html.twig', [
            'form' => $form->createView(),
            'categoriePlat' => $categoriePlat,
        ]);
    }

    #[Route("/categorieplat/delete/{id}", name: "app_categorieplat_delete")]
    public function delete(int $id, EntityManagerInterface $entityManager, CategoriePlatRepository $categoriePlatRepository): Response
    {
        $categoriePlat = $categoriePlatRepository->find($id);

        if (!$categoriePlat) {
            throw $this->createNotFoundException('La catégorie n\'existe pas.');
        }

        $entityManager->remove($categoriePlat);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès !');

        return $this->redirectToRoute('app_categorieplat');
    }

    #[Route("/categories", name: "liste_categories")]
    public function listeCategories(CategoriePlatRepository $categoriePlatRepository): Response
    {
        $categoriesPlat = $categoriePlatRepository->findAll();

        return $this->render('fronttemplates/listecategorie.html.twig', [
            'categoriesPlat' => $categoriesPlat
        ]);
    }
}
