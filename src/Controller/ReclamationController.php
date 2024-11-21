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

        $reclamations=$rep->findAll();
        return $this->render('reclamation/afficherReclamations.html.twig', [
            'reclamations' => $reclamations,
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

}
