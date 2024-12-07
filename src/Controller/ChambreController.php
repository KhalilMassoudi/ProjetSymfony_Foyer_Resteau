<?php
namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChambreController extends AbstractController
{
    #[Route("/chambre", name: "app_chamber")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        SluggerInterface $slugger
    ): Response {
        $chambre = new Chambre();
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $chambreRepository->handleImageUpload(
                    $imageFile,
                    $slugger,
                    $this->getParameter('images_directory')
                );

                if ($newFilename === null) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_chamber');
                }

                $chambre->setImage($newFilename);
            }

            $entityManager->persist($chambre);
            $entityManager->flush();

            $this->addFlash('success', 'Chambre ajoutée avec succès !');
            return $this->redirectToRoute('app_chamber');
        }

        $chambres = $chambreRepository->findAll();

        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
        ]);
    }

    // Dans votre contrôleur
    #[Route("/chambre/search", name: "app_chambre_search")]
    public function search(Request $request, ChambreRepository $chambreRepository): Response
    {
        // Récupération des critères de recherche depuis la requête
        $numero = $request->query->get('numeroChB', '');  // Le champ de recherche par numéro de chambre
        $etage = $request->query->get('etageChB', '');  // Le champ de recherche par étage
        $capacite = $request->query->get('capaciteChB', '');  // Le champ de recherche par capacité
        $statut = $request->query->get('statutChB', '');  // Le champ de recherche par statut
        $prix = $request->query->get('prixChB', '');  // Le champ de recherche par prix exact

        // Création d'un tableau avec les critères de recherche
        $searchTerms = [
            'numeroChB' => $numero,
            'etageChB' => $etage,
            'capaciteChB' => $capacite,
            'statutChB' => $statut,
            'prixChB' => $prix,  // Utilisation du champ de prix exact
        ];

        // Appel à la méthode de recherche avec le tableau de critères
        $chambres = $chambreRepository->findByTerm($searchTerms);

        return $this->render('backtemplates/app_search_chambre.html.twig', [
            'chambres' => $chambres,
            'numero' => $numero,
            'etage' => $etage,
            'capacite' => $capacite,
            'statut' => $statut,
            'prix' => $prix,  // Passage du prix exact à la vue
        ]);
    }

    #[Route("/chambre/edit/{id}", name: "app_chambre_edit")]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        SluggerInterface $slugger
    ): Response {
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $chambreRepository->handleImageUpload(
                    $imageFile,
                    $slugger,
                    $this->getParameter('images_directory')
                );

                if ($newFilename === null) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_chambre_edit', ['id' => $id]);
                }

                $chambre->setImage($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Chambre modifiée avec succès !');
            return $this->redirectToRoute('app_chamber');
        }

        return $this->render('backtemplates/app_edit_chambre.html.twig', [
            'form' => $form->createView(),
            'chambre' => $chambre,
        ]);
    }

    #[Route("/chambre/delete/{id}", name: "app_chambre_delete")]
    public function delete(
        int $id,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository
    ): Response {
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        $entityManager->remove($chambre);
        $entityManager->flush();

        $this->addFlash('success', 'Chambre supprimée avec succès !');
        return $this->redirectToRoute('app_chamber');
    }

    #[Route("/front/chambre", name: "app_front_chambre")]
    public function frontChambre(Request $request, ChambreRepository $chambreRepository): Response {
        // Récupérer les termes de recherche depuis la requête (valeurs de recherche, par exemple par numéro, étage, etc.)
        $searchTerms = [
            'numeroChB' => $request->query->get('numeroChB', ''), // Récupérer les paramètres de recherche
            'etageChB' => $request->query->get('etageChB', ''),
            'capaciteChB' => $request->query->get('capaciteChB', ''),
            'statutChB' => $request->query->get('statutChB', ''),
            'prixChB' => $request->query->get('prixChB', ''),
        ];

        // Appel à la méthode de recherche avec les critères
        $chambres = $chambreRepository->findByTerm($searchTerms);

        return $this->render('fronttemplates/app_frontchambre.html.twig', [
            'chambres' => $chambres,
            'searchTerms' => $searchTerms, // Passer les termes de recherche à la vue
        ]);
    }

}
