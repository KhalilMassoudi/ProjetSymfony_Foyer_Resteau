<?php

namespace App\Controller;

use App\Entity\TypeReclamation; // Entité TypeReclamation
use App\Form\TypereclamationType; // Formulaire TypeReclamationType
use App\Repository\TypeReclamationRepository;
use Doctrine\Persistence\ManagerRegistry; // Pour ManagerRegistry
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // Utilisation des annotations pour les routes
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

class TypeReclamationController extends AbstractController
{

    #[Route('/type/reclamation', name: 'app_type_reclamation')]
    public function index(): Response
    {
        return $this->render('type_reclamation/index.html.twig', [
            'controller_name' => 'TypeReclamationController',
        ]);
    }

    #[Route('/typereclamation/add', name: 'app_type_reclamation_add')]
    public function addtypereclamation(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        // Créer une nouvelle instance de TypeReclamation
        $typeReclamation = new TypeReclamation();

        // Créer le formulaire
        $form = $this->createForm(TypereclamationType::class, $typeReclamation);

        // Gérer la requête et manipuler le formulaire
        $form->handleRequest($request);

        // Validation manuelle de l'entité
        $entityErrors = $validator->validate($typeReclamation); // Valide l'entité

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted()) {
            // Si des erreurs de validation sont présentes
            if (count($entityErrors) > 0 || !$form->isValid()) {
                // Ajouter des messages flash pour les erreurs du formulaire et de l'entité
                foreach ($entityErrors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }

                // Ajouter des erreurs du formulaire
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            } else {
                // Sauvegarder les données dans la base
                $entityManager = $doctrine->getManager();
                $entityManager->persist($typeReclamation);
                $entityManager->flush();

                // Redirection avec un message de succès
                $this->addFlash('success', 'Type de réclamation ajouté avec succès !');
                return $this->redirectToRoute('app_type_reclamation_add');
            }
        }

        // Récupérer tous les types de réclamation depuis la base
        $repository = $doctrine->getRepository(TypeReclamation::class);
        $typesReclamation = $repository->findAll();

        // Rendre la vue avec le formulaire et la liste des types de réclamation
        return $this->render('backtemplates/typesreclamation.html.twig', [
            'form' => $form->createView(),
            'typesReclamations' => $typesReclamation,
        ]);
    }




    #[Route('/typereclamation/delete/{id}', name: 'app_type_reclamation_delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        // Récupérer l'entité TypeReclamation par son identifiant
        $repository = $doctrine->getRepository(TypeReclamation::class);
        $typeReclamation = $repository->find($id);

        if (!$typeReclamation) {
            // Si l'entité n'existe pas, afficher un message d'erreur
            $this->addFlash('error', 'Type de réclamation introuvable.');
            return $this->redirectToRoute('app_type_reclamation_add');
        }

        // Supprimer l'entité de la base de données
        $entityManager = $doctrine->getManager();
        $entityManager->remove($typeReclamation);
        $entityManager->flush();

        // Afficher un message de succès
        $this->addFlash('success', 'Type de réclamation supprimé avec succès.');

        // Rediriger vers la page d'index des types de réclamation
        return $this->redirectToRoute('app_type_reclamation_add');
    }
    #[Route("/typereclamation/edit/{id}", name: "app_type_reclamation_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, TypeReclamationRepository $typeReclamationRepository): Response
    {
        // Récupérer le type de réclamation par ID
        $typeReclamation = $typeReclamationRepository->find($id);

        // Vérifier si le type de réclamation existe
        if (!$typeReclamation) {
            throw $this->createNotFoundException('Le type de réclamation n\'existe pas.');
        }

        // Créer le formulaire pour éditer le type de réclamation
        $form = $this->createForm(TypereclamationType::class, $typeReclamation);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Type de réclamation modifié avec succès !');

            // Rediriger vers la liste des types de réclamations
            return $this->redirectToRoute('app_type_reclamation_add');
        }

        // Afficher le formulaire d'édition
        return $this->render('backtemplates/app_edit_type_reclamation.html.twig', [
            'form' => $form->createView(),
            'typeReclamation' => $typeReclamation,
        ]);
    }


}
