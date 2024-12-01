<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;// operations BDD
use Doctrine\ORM\EntityManagerInterface;//manipuler base de donnes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatController extends AbstractController
{
    /**
     * Méthode pour afficher la liste des plats et permettre l'ajout d'un nouveau plat.
     */
    #[Route("/plat", name: "app_plat")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        PlatRepository $platRepository
    ): Response {
        // Créer une nouvelle instance du plat
        $plat = new Plat();

        // Créer le formulaire pour ajouter un nouveau plat
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ajout plat persistance dans la bdd 
            $entityManager->persist($plat);// preparer l entité
            $entityManager->flush();//ecr bdd

  
            $this->addFlash('success', 'Plat ajouté avec succès !');

            return $this->redirectToRoute('app_plat');
        }

        // Récupérer plats(injecter la repo)
        $plats = $platRepository->findAll();

       
        return $this->render('backtemplates/app_plat.html.twig', [
            'form' => $form->createView(),
            'plats' => $plats,
        ]);
    }


    #[Route("/plat/edit/{id}", name: "app_plat_edit")]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        PlatRepository $platRepository
    ): Response {
        // injecter repo
        $plat = $this->findPlatOr404($platRepository, $id);

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
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        PlatRepository $platRepository
    ): Response {
        // Trouver le plat ou lancer une exception 404
        $plat = $this->findPlatOr404($platRepository, $id);

        // Supprimer le plat
        $entityManager->remove($plat);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'Plat supprimé avec succès !');

        // Rediriger vers la liste des plats
        return $this->redirectToRoute('app_plat');
    }


    private function findPlatOr404(PlatRepository $platRepository, int $id): Plat
    {
        $plat = $platRepository->find($id);
        if (!$plat) {
            throw $this->createNotFoundException('Le plat n\'existe pas.');
        }
        return $plat;
    }
    #[Route("/plats", name: "app_plats_list")]
    public function listAllPlats(PlatRepository $platRepository): Response
    {
        // Récupérer tous les plats
        $plats = $platRepository->findAll();
    
        // Rendre la vue avec la liste des plats
        return $this->render('fronttemplates/plat_details.html.twig', [
            'plats' => $plats,
        ]);
    }
    

}
