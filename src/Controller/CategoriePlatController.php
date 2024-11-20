<?php

namespace App\Controller;

use App\Entity\CategoriePlat;
use App\Form\CategoriePlatType;
use App\Repository\CategoriePlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriePlatController extends AbstractController
{/*done*/
    #[Route('/categorie/plat', name: 'app_categorie_plat_index')]
    public function index(CategoriePlatRepository $categoriePlatRepository): Response
    {
        return $this->render('categorie_plat/index.html.twig', [
            'categories' => $categoriePlatRepository->findAll(),
        ]);
    }
/*done*/
    #[Route('/categorie/plat/new', name: 'app_categorie_plat_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoriePlat = new CategoriePlat();
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoriePlat);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_plat_index');
        }

        return $this->render('categorie_plat/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
/*done*/
    #[Route('/categorie/plat/{id}', name: 'app_categorie_plat_show')]
    public function show(CategoriePlat $categoriePlat): Response
    {
        return $this->render('categorie_plat/show.html.twig', [
            'categorie' => $categoriePlat,
        ]);
    }
/*done*/
    #[Route('/categorie/plat/{id}/edit', name: 'app_categorie_plat_edit')]
    public function edit(Request $request, CategoriePlat $categoriePlat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriePlatType::class, $categoriePlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_plat_index');
        }

        return $this->render('categorie_plat/edit.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categoriePlat,
        ]);
    }

    #[Route('/categorie/plat/{id}/delete', name: 'app_categorie_plat_delete', methods: ['POST'])]
    public function delete(CategoriePlat $categoriePlat, EntityManagerInterface $entityManager): Response
    {
        // Supprimer la catégorie
        $entityManager->remove($categoriePlat);
        $entityManager->flush();
    
        // Ajouter un message flash de succès
        $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
    
        // Rediriger vers la liste des catégories
        return $this->redirectToRoute('app_categorie_plat_index');
    }
    
    
}
