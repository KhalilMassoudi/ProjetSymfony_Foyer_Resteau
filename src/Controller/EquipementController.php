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
    #[Route("/equipement", name: "app_equipement")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipement);
            $entityManager->flush();

            $this->addFlash('success', 'Équipement ajouté avec succès !');
            return $this->redirectToRoute('app_equipement');
        }

        $equipements = $equipementRepository->findAll();

        return $this->render('backtemplates/app_equipement.html.twig', [
            'form' => $form->createView(),
            'equipements' => $equipements,
        ]);
    }

    #[Route("/equipement/search", name: "app_equipement_search")]
    public function search(Request $request, EquipementRepository $equipementRepository): Response
    {
        $nomEquipement = $request->query->get('nomEquipementB', '');
        $etatEquipement = $request->query->get('etatEquipementB', '');
        $numeroChB = $request->query->get('chambre', '');

        $searchTerms = [
            'nomEquipementB' => $nomEquipement,
            'etatEquipementB' => $etatEquipement,
            'numeroChB' => $numeroChB,
        ];


        $equipements = $equipementRepository->findByTerm($searchTerms);

        return $this->render('backtemplates/app_search_equipement.html.twig', [
            'equipements' => $equipements,
            'nomEquipementB' => $nomEquipement,
            'etatEquipementB' => $etatEquipement,
            'chambre' => $numeroChB,
        ]);
    }


    #[Route("/equipement/edit/{id}", name: "app_equipement_edit")]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        $equipement = $equipementRepository->find($id);

        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }

        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Équipement modifié avec succès !');
            return $this->redirectToRoute('app_equipement');
        }

        return $this->render('backtemplates/app_edit_equipement.html.twig', [
            'form' => $form->createView(),
            'equipement' => $equipement,
        ]);
    }

    #[Route("/equipement/delete/{id}", name: "app_equipement_delete")]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        EquipementRepository $equipementRepository
    ): Response {
        $equipement = $equipementRepository->find($id);

        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }

        $entityManager->remove($equipement);
        $entityManager->flush();

        $this->addFlash('success', 'Équipement supprimé avec succès !');
        return $this->redirectToRoute('app_equipement');
    }

    #[Route("/front/equipement", name: "app_front_equipement")]
    public function frontEquipement(Request $request, EquipementRepository $equipementRepository): Response
    {
        // Récupération de la chaîne de recherche
        $searchTerm = $request->query->get('searchTerm', '');

        // Préparer les termes de recherche
        $searchTerms = [
            'searchTerm' => $searchTerm,
        ];

        // Rechercher les équipements en fonction des termes
        $equipements = $equipementRepository->findByTerm($searchTerms);

        return $this->render('fronttemplates/app_frontequipement.html.twig', [
            'equipements' => $equipements,
            'searchTerms' => $searchTerms,
        ]);
    }

}
