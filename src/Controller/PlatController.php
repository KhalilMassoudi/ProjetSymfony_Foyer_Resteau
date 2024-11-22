<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatController extends AbstractController
{
    #[Route("/plat", name: "app_plat")]
    public function index(Request $request, EntityManagerInterface $entityManager, PlatRepository $platRepository): Response
    {
        // Créer une nouvelle instance de Plat
        $plat = new Plat();

        // Créer le formulaire pour ajouter un nouveau plat
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le nouveau plat dans la base de données
            $entityManager->persist($plat);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Plat ajouté avec succès !');

            // Rediriger vers la liste des plats
            return $this->redirectToRoute('app_plat');
        }

        // Récupérer la liste des plats
        $plats = $platRepository->findAll();

        // Rendre la vue avec le formulaire et la liste des plats
        return $this->render('backtemplates/app_plat.html.twig', [
            'form' => $form->createView(),
            'plats' => $plats,
        ]);
    }

    #[Route("/plat/edit/{id}", name: "app_plat_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, PlatRepository $platRepository): Response
    {
        // Récupérer le plat par ID
        $plat = $platRepository->find($id);

        // Vérifier si le plat existe
        if (!$plat) {
            throw $this->createNotFoundException('Le plat n\'existe pas.');
        }

        // Créer le formulaire pour éditer le plat
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Plat modifié avec succès !');

            // Rediriger vers la liste des plats
            return $this->redirectToRoute('app_plat');
        }

        // Afficher le formulaire d'édition
        return $this->render('backtemplates/app_edit_plat.html.twig', [
            'form' => $form->createView(),
            'plat' => $plat,
        ]);
    }

    #[Route("/plat/delete/{id}", name: "app_plat_delete")]
    public function delete(int $id, EntityManagerInterface $entityManager, PlatRepository $platRepository): Response
    {
        // Récupérer le plat par ID
        $plat = $platRepository->find($id);

        // Vérifier si le plat existe
        if (!$plat) {
            throw $this->createNotFoundException('Le plat n\'existe pas.');
        }

        // Supprimer le plat
        $entityManager->remove($plat);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Plat supprimé avec succès !');

        // Rediriger vers la liste des plats
        return $this->redirectToRoute('app_plat');
    }
}
