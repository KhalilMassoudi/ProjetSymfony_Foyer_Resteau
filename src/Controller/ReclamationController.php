<?php

namespace App\Controller;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;




class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
    #[Route('/afficherreclamations', name: 'app_afficherReclamations')]
    public function afficherReclamation(ReclamationRepository $rep): Response
    {
        // Récupérer toutes les réclamations
        $reclamations = $rep->findAll();

        // Créer un formulaire vide pour modification
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer la liste des réclamations et le formulaire de modification au template
        return $this->render('reclamation/afficherReclamations.html.twig', [
            'reclamations' => $reclamations,
            'formModif' => $formModif->createView(),
            'formAjout' => $formModif->createView()
        ]);
    }
    #[Route('/backreclamationdetails/{id}', name: 'app_reclamation_details', methods: ['GET'])]
    public function details(int $id, ManagerRegistry $doctrine): Response
    {
        // Récupérer la réclamation par son ID
        $reclamation = $doctrine->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException("La réclamation avec l'ID $id n'existe pas.");
        }

        // Retourner la vue avec les détails de la réclamation
        return $this->render('backtemplates/backreclamationdetails.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/adminafficherreclamations', name: 'app_adminafficherReclamations')]
    public function adminafficherReclamation(ReclamationRepository $rep): Response
    {

        // Récupérer toutes les réclamations
        $reclamations = $rep->findAll();

        // Créer un formulaire vide pour modification
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer la liste des réclamations et le formulaire de modification au template
        return $this->render('backtemplates/backreclamations.html.twig', [
            'reclamations' => $reclamations,
            'formModif' => $formModif->createView()

        ]);
    }




    #[Route('/ajoutReclamation', name: 'app_ajouterReclamation')]
    public function ajoutReclamation(ManagerRegistry $doctrine, Request $request): Response
    {
        // Création d'une nouvelle instance de Reclamation
        $reclamation = new Reclamation();

        // Création du formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Traitement des données saisies dans le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'Entity Manager
            $em = $doctrine->getManager();
            // Persistance de la réclamation dans la base de données
            $em->persist($reclamation);
            // Sauvegarde des données
            $em->flush();

            // Redirection vers la page d'affichage des réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('reclamation/ajoutReclamation.html.twig', [
            'f' => $form->createView(), // Passer la vue du formulaire à la vue Twig
        ]);
    }
    #[Route('/modifierReclamation/{id}', name: 'app_modifierReclamation')]
    public function modifierReclamation(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        // Récupération de l'Entity Manager
        $em = $doctrine->getManager();

        // Récupération de la réclamation à modifier
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Si la réclamation n'existe pas, afficher une erreur
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Création du formulaire pré-rempli avec les données existantes
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Traitement des données saisies dans le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Les données sont automatiquement mises à jour dans l'entité via le formulaire
            // Sauvegarde des modifications
            $em->flush();

            // Redirection vers la page d'affichage des réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('reclamation/ajoutReclamation.html.twig', [
            'f' => $form->createView(), // Passer la vue du formulaire à la vue Twig
        ]);
    }
    #[Route('/supprimerReclamation/{id}', name: 'app_supprimerReclamation', methods: ['POST', 'GET'])]
    public function supprimerReclamation(ManagerRegistry $doctrine, int $id): Response
    {
        // Récupérer l'Entity Manager
        $em = $doctrine->getManager();

        // Récupérer la réclamation par son ID
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            $this->addFlash('error', 'Réclamation introuvable.');
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Supprimer la réclamation
        $em->remove($reclamation);
        $em->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Réclamation supprimée avec succès.');

        // Rediriger vers la liste des réclamations
        return $this->redirectToRoute('app_afficherReclamations');
    }
    #[Route('/modifierReclamationn/{id}', name: 'app_modifierReclamationn', methods: ['GET', 'POST'])]
    public function modifierReclamationn($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        // Trouver la réclamation par son ID
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Si la réclamation n'existe pas, lancer une exception
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Créer le formulaire pré-rempli avec les données existantes
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la réclamation mise à jour
            $em->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Réclamation modifiée avec succès.');

            // Rediriger vers la page qui liste les réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Renvoyer la page avec le formulaire
        return $this->render('reclamation/afficherReclamations.html.twig', [
            'formModif' => $form->createView(),  // Passer le formulaire au template
        ]);
    }



    #[Route('/ajouterReclamationn', name: 'app_ajouterReclamationn', methods: ['POST'])]
    public function ajouterReclamationn(ManagerRegistry $doctrine, Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($reclamation);  // Persist the new reclamation
            $em->flush();  // Save it to the database

            // Flash message for success
            $this->addFlash('success', 'Réclamation ajoutée avec succès.');

            // Redirect to the list of reclamations or stay on the current page
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // If form is not valid, display the form with error messages
        $this->addFlash('error', 'Erreur lors de l\'ajout de la réclamation.');

        // Re-render the same page
        return $this->redirectToRoute('app_afficherReclamations');
    }

    #[Route('/reclamation/repondre/{id}', name: 'app_reclamation_repondre')]
    public function repondreReclamation(
        int $id,
        Request $request,
        ReclamationRepository $rep,
        ManagerRegistry $doctrine
    ): Response {
        // Récupérer la réclamation par ID
        $reclamation = $rep->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable.');
        }

        // Créer un formulaire pour la réponse (seulement le champ "reponse")
        $form = $this->createFormBuilder($reclamation)
            ->add('reponse', TextareaType::class, [
                'label' => 'Votre réponse',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->getForm();

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la réponse dans la base de données
            $em = $doctrine->getManager();
            $em->persist($reclamation);
            $em->flush();

            // Ajouter un message flash et rediriger vers la liste des réclamations
            $this->addFlash('success', 'Réponse envoyée avec succès.');
            return $this->redirectToRoute('app_adminafficherReclamations');
        }

        // Renvoyer le formulaire de réponse à la vue
        return $this->render('backtemplates/repondreReclamation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }



}