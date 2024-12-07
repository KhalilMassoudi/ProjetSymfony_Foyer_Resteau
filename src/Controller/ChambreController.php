<?php
namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Enum\ChambreStatut;
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
    #[Route("/chambre/search", name: "app_chambre_search")]
    public function search(Request $request, ChambreRepository $chambreRepository): Response
    {
        // Récupérer les valeurs de la requête pour chaque critère
        $numero = $request->query->get('numeroChB', '');
        $etageMin = $request->query->get('etage_min', 1); // Valeur par défaut 1
        $etageMax = $request->query->get('etage_max', 10); // Valeur par défaut 10
        $capaciteMin = $request->query->get('capacite_min', 1); // Valeur par défaut 1
        $capaciteMax = $request->query->get('capacite_max', 5); // Valeur par défaut 5
        $statut = $request->query->get('statutChB', ''); // Statut de chambre
        $prixMin = $request->query->get('prix_min', '');
        $prixMax = $request->query->get('prix_max', '');

        // Créer un tableau avec les critères
        $searchTerms = [
            'numeroChB' => $numero,
            'etage_min' => $etageMin,
            'etage_max' => $etageMax,
            'capacite_min' => $capaciteMin,
            'capacite_max' => $capaciteMax,
            'statutChB' => $statut,
            'prix_min' => $prixMin,
            'prix_max' => $prixMax,
        ];

        // Effectuer la recherche et le filtrage via le repository
        $chambres = $chambreRepository->searchAndFilter($searchTerms);

        // Retourner les résultats avec les critères utilisés pour afficher les options du formulaire
        return $this->render('backtemplates/app_search_chambre.html.twig', [
            'chambres' => $chambres,
            'searchTerms' => $searchTerms,
            'statuts' => array_map(fn($statut) => $statut->getValue(), ChambreStatut::cases()), // Convertir les statuts en chaînes de caractères
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
    public function frontChambre(Request $request, ChambreRepository $chambreRepository): Response
    {
        $searchTerms = [
            'numeroChB' => $request->query->get('numeroChB', ''),
            'etage_min' => $request->query->get('etage_min', 1),
            'etage_max' => $request->query->get('etage_max', 10),
            'capacite_min' => $request->query->get('capacite_min', 1),
            'capacite_max' => $request->query->get('capacite_max', 5),
            'statutChB' => $request->query->get('statutChB', ''),
            'prix_min' => $request->query->get('prix_min', ''),
            'prix_max' => $request->query->get('prix_max', ''),
        ];

        $chambres = $chambreRepository->searchAndFilter($searchTerms);

        return $this->render('fronttemplates/app_frontchambre.html.twig', [
            'chambres' => $chambres,
            'searchTerms' => $searchTerms,
            'statuts' => array_map(fn($statut) => $statut->getValue(), ChambreStatut::cases()), // Convertir les statuts en chaînes de caractères
        ]);
    }
}
