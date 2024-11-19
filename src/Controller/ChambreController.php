<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use App\Enum\ChambreStatut;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

class ChambreController extends AbstractController
{
    #[Route("/chambre", name: "app_chamber")]
    public function index(Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository, FormFactoryInterface $formFactory): Response
    {
        // Créer une nouvelle instance de Chambre
        $chambre = new Chambre();

        // Créer le formulaire pour ajouter une nouvelle chambre
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier et assigner le statut de chambre à partir de l'énumération
            $statut = $form->get('statutChB')->getData();
            $chambre->setStatutChB($statut);

            // Sauvegarder la nouvelle chambre dans la base de données
            $entityManager->persist($chambre);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Chambre ajoutée avec succès !');

            // Rediriger vers la liste des chambres
            return $this->redirectToRoute('app_chamber');
        }

        // Récupérer la liste des chambres
        $chambres = $chambreRepository->findAll();

        // Rendre la vue avec le formulaire et la liste des chambres
        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
        ]);
    }

    #[Route("/chambre/edit/{idChB}", name: "app_chambre_edit")]
    public function edit(int $idChB, Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        // Récupérer la chambre par ID
        $chambre = $chambreRepository->find($idChB);

        // Vérifier si la chambre existe
        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        // Créer le formulaire pour éditer la chambre
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier et assigner le statut de chambre à partir de l'énumération
            $statut = $form->get('statutChB')->getData();
            $chambre->setStatutChB($statut);

            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Chambre modifiée avec succès !');

            // Rediriger vers la liste des chambres
            return $this->redirectToRoute('app_chamber');
        }

        // Afficher le formulaire d'édition
        return $this->render('backtemplates/app_edit_chambre.html.twig', [
            'form' => $form->createView(),
            'chambre' => $chambre,
        ]);
    }

    #[Route("/chambre/delete/{idChB}", name: "app_chambre_delete")]
    public function delete(int $idChB, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        // Récupérer la chambre par ID
        $chambre = $chambreRepository->find($idChB);

        // Vérifier si la chambre existe
        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        // Supprimer la chambre
        $entityManager->remove($chambre);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Chambre supprimée avec succès !');

        // Rediriger vers la liste des chambres
        return $this->redirectToRoute('app_chamber');
    }
}