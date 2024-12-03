<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EquipementController extends AbstractController
{
    #[Route("/equipement", name: "app_equipement")]
    public function index(Request $request, EntityManagerInterface $entityManager, EquipementRepository $equipementRepository, SluggerInterface $slugger): Response
    {
        // Créer une nouvelle instance d'Equipement
        $equipement = new Equipement();

        // Créer le formulaire
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer l'image téléchargée et la traiter
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Créer un nom unique pour le fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire d'upload
                try {
                    $imageFile->move(
                        $this->getParameter('equipements_directory'), // Répertoire d'upload défini dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // En cas d'erreur, ajouter un message flash
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_equipement');
                }

                // Assigner le nom du fichier à l'entité Equipement
                $equipement->setImage($newFilename);
            }

            // Enregistrer l'entité dans la base de données
            $entityManager->persist($equipement);
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Equipement ajouté avec succès !');

            // Rediriger vers la page d'index
            return $this->redirectToRoute('app_equipement');
        }

        // Récupérer tous les équipements pour les afficher
        $equipements = $equipementRepository->findAll();

        return $this->render('backtemplates/app_equipement.html.twig', [
            'form' => $form->createView(),
            'equipements' => $equipements,
        ]);
    }

    #[Route("/equipement/edit/{id}", name: "app_equipement_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, EquipementRepository $equipementRepository, SluggerInterface $slugger): Response
    {
        // Récupérer l'équipement par ID
        $equipement = $equipementRepository->find($id);

        // Vérifier si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }

        // Créer le formulaire pour l'équipement
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer l'image téléchargée et la traiter
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Créer un nom unique pour le fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire d'upload
                try {
                    $imageFile->move(
                        $this->getParameter('equipements_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // En cas d'erreur, ajouter un message flash
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_equipement');
                }

                // Assigner le nom du fichier à l'entité Equipement
                $equipement->setImage($newFilename);
            }

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Equipement modifié avec succès !');

            // Rediriger vers la page d'index
            return $this->redirectToRoute('app_equipement');
        }

        return $this->render('backtemplates/app_edit_equipement.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }

    #[Route("/equipement/delete/{id}", name: "app_equipement_delete")]
    public function delete(int $id, EntityManagerInterface $entityManager, EquipementRepository $equipementRepository): Response
    {
        // Récupérer l'équipement par ID
        $equipement = $equipementRepository->find($id);

        // Vérifier si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }

        // Supprimer l'équipement
        $entityManager->remove($equipement);
        $entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', 'Equipement supprimé avec succès !');

        // Rediriger vers la page d'index
        return $this->redirectToRoute('app_equipement');
    }

    #[Route("/front/equipement", name: "app_front_equipement")]
    public function frontEquipement(EquipementRepository $equipementRepository): Response
    {
        // Récupération de tous les équipements pour les afficher
        $equipements = $equipementRepository->findAll();

        // Rendu de la vue pour l'affichage des équipements
        return $this->render('fronttemplates/app_frontequipement.html.twig', [
            'equipements' => $equipements,
            'noEquipement' => empty($equipements) // Ajouter une variable pour indiquer qu'il n'y a pas d'équipements
        ]);
    }

}
