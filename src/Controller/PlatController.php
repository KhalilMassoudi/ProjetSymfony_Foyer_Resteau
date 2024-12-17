<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Form\AdvancedSearchType;
use App\Repository\PlatRepository;
use App\Repository\CategoriePlatRepository; // Corriger l'importation ici
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatController extends AbstractController
{
    #[Route("/plat", name: "app_plat")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        PlatRepository $platRepository
    ): Response {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $plat->setImage($newFilename);
            }

            $entityManager->persist($plat);
            $entityManager->flush();

            $this->addFlash('success', 'Plat ajouté avec succès !');
            return $this->redirectToRoute('app_plat');
        }

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
        $plat = $this->findPlatOr404($platRepository, $id);

        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                if ($plat->getImage()) {
                    $oldFilePath = $this->getParameter('images_directory') . '/' . $plat->getImage();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $plat->setImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Plat modifié avec succès !');
            return $this->redirectToRoute('app_plat');
        }

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
        $plat = $this->findPlatOr404($platRepository, $id);

        if ($plat->getImage()) {
            $filePath = $this->getParameter('images_directory') . '/' . $plat->getImage();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $entityManager->remove($plat);
        $entityManager->flush();

        $this->addFlash('success', 'Plat supprimé avec succès !');
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

    #[Route("/front/plats", name: "app_plats_list")]
    public function listAllPlats(PlatRepository $platRepository): Response
    {
        $plats = $platRepository->findAll();

        return $this->render('fronttemplates/plat_details.html.twig', [
            'plats' => $plats,
        ]);
    }

    #[Route("/plat/search", name: "app_plat_search")]
    public function search(
        Request $request,
        PlatRepository $platRepository,
        CategoriePlatRepository $categoriePlatRepository // Correct repository import
    ): Response {
        // Récupérer les valeurs de la requête pour chaque critère
        $nomPlat = $request->query->get('nomPlat', '');  // Nom du plat
        $prixMin = $request->query->get('prix_min', ''); // Prix minimum
        $prixMax = $request->query->get('prix_max', ''); // Prix maximum
        $categoriePlat = $request->query->get('categoriePlat', ''); // Catégorie du plat

        // Créer un tableau avec les critères
        $searchTerms = [
            'nomPlat' => $nomPlat,
            'prix_min' => $prixMin,
            'prix_max' => $prixMax,
            'categoriePlat' => $categoriePlat,
        ];

        // Effectuer la recherche et le filtrage via le repository
        $plats = $platRepository->searchAndFilter($searchTerms);

        // Récupérer toutes les catégories pour la liste déroulante
        $categories = $categoriePlatRepository->findAll();

        // Retourner les résultats avec les critères utilisés pour afficher les options du formulaire
        return $this->render('backtemplates/app_search_plat.html.twig', [
            'plats' => $plats,
            'searchTerms' => $searchTerms,
            'categories' => $categories,  // Passer les catégories pour la sélection
        ]);
    }
}
