<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipementController extends AbstractController
{
    /**
     * Méthode pour afficher la liste des équipements et permettre l'ajout d'un nouvel équipement.
     */
    #[Route("/equipement", name: "app_equipement")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        // Créer une nouvelle instance de l'équipement
        $equipement = new Equipement();

        // Créer le formulaire pour ajouter un nouvel équipement
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder le nouvel équipement dans la base de données
            $entityManager->persist($equipement);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Équipement ajouté avec succès !');

            // Rediriger vers la liste des équipements
            return $this->redirectToRoute('app_equipement');
        }

        // Récupérer la liste des équipements
        $equipements = $equipementRepository->findAll();

        // Rendre la vue avec le formulaire et la liste des équipements
        return $this->render('backtemplates/app_equipement.html.twig', [
            'form' => $form->createView(),
            'equipements' => $equipements,
        ]);
    }

    /**
     * Méthode pour modifier un équipement existant.
     */
    #[Route("/equipement/edit/{idEquipementB}", name: "app_equipement_edit")]
    public function edit(
        int $idEquipementB,
        Request $request,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        // Trouver l'équipement ou lancer une exception 404
        $equipement = $this->findEquipementOr404($equipementRepository, $idEquipementB);

        // Créer le formulaire pour éditer l'équipement
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Équipement modifié avec succès !');

            // Rediriger vers la liste des équipements
            return $this->redirectToRoute('app_equipement');
        }

        // Afficher le formulaire d'édition
        return $this->render('backtemplates/app_edit_equipement.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }

    /**
     * Méthode pour supprimer un équipement existant.
     */
    #[Route("/equipement/delete/{idEquipementB}", name: "app_equipement_delete")]
    public function delete(
        int $idEquipementB,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        // Trouver l'équipement ou lancer une exception 404
        $equipement = $this->findEquipementOr404($equipementRepository, $idEquipementB);

        // Supprimer l'équipement
        $entityManager->remove($equipement);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Équipement supprimé avec succès !');

        // Rediriger vers la liste des équipements
        return $this->redirectToRoute('app_equipement');
    }

    /**
     * Méthode privée pour trouver un équipement ou retourner une erreur 404.
     */
    private function findEquipementOr404(EquipementRepository $equipementRepository, int $idEquipementB): Equipement
    {
        $equipement = $equipementRepository->find($idEquipementB);
        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }
        return $equipement;
    }
}
