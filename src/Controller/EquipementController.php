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
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $searchTerm = $request->query->get('searchTerm', '');
        $searchTerms = [
            'searchTerm' => $searchTerm,
        ];
        $equipements = $equipementRepository->findByTerm($searchTerms);
        return $this->render('backtemplates/app_search_equipement.html.twig', [
            'equipements' => $equipements,
            'searchTerm' => $searchTerm,  // Transmettre le terme de recherche à la vue
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
        $searchTerm = $request->query->get('searchTerm', '');
        $searchTerms = [
            'searchTerm' => $searchTerm,
        ];
        $equipements = $equipementRepository->findByTerm($searchTerms);
        return $this->render('fronttemplates/app_frontequipement.html.twig', [
            'equipements' => $equipements,
            'searchTerms' => $searchTerms,
        ]);
    }


    #[Route('/rate-equipement/{id}', name: 'rate_equipement', methods: ['POST'])]
    public function rateEquipement($id, Request $request, EquipementRepository $equipementRepo): JsonResponse
    {
        $content = $request->getContent();
        // Débogage pour voir les données JSON transmises
        file_put_contents('php://stderr', "Requête JSON reçue : $content\n");

        $data = json_decode($content, true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON structure'], 400);
        }

        $rating = $data['rating'] ?? null;

        if ($rating === null || $rating < 1 || $rating > 5) {
            return new JsonResponse(['error' => 'Invalid rating value'], 400);
        }

        $equipement = $equipementRepo->find($id);

        if (!$equipement) {
            return new JsonResponse(['error' => 'Equipment not found'], 404);
        }

        // Débogage pour voir si l'équipement est chargé
        file_put_contents(
            'php://stderr',
            "Équipement trouvé : ID = {$id}, Ancienne Note = {$equipement->getRating()}\n"
        );

        // Mettre à jour la note
        $equipement->setRating($rating);

        // Sauvegarder l'objet dans la base de données
        $equipementRepo->save($equipement, true);

        return new JsonResponse(['success' => true, 'rating' => $rating, 'equipement_id' => $id], 200);
    }
}
