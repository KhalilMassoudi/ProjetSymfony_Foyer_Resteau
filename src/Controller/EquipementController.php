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
        // Créer une instance de l'équipement et le formulaire
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        // Gérer le formulaire soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le fichier d'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Nettoyage et sécurisation du nom du fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire spécifié
                $imageFile->move(
                    $this->getParameter('equipements_directory'),
                    $newFilename
                );

                // Mettre à jour l'entité avec le chemin de l'image
                $equipement->setImage($newFilename);
            }

            // Enregistrer l'entité en base de données
            $entityManager->persist($equipement);
            $entityManager->flush();

            // Ajouter un message flash pour indiquer le succès
            $this->addFlash('success', 'Équipement ajouté avec succès avec une image !');
            return $this->redirectToRoute('app_equipement');
        }

        // Récupérer tous les équipements pour l'affichage
        $equipements = $equipementRepository->findAll();

        return $this->render('backtemplates/app_equipement.html.twig', [
            'form' => $form->createView(),
            'equipements' => $equipements,
        ]);
    }
    #[Route('/equipements/pdf', name: 'app_equipements_pdf')]
    public function generatePdf(EquipementRepository $equipementRepository): Response
    {
        // Étape 1 : Configurez DomPDF
        $pdfOptions = new \Dompdf\Options();
        $pdfOptions->set('defaultFont', 'Arial'); // Définir la police par défaut (facultatif)
        $pdfOptions->setIsHtml5ParserEnabled(true); // Activer les balises HTML5 si nécessaires
        $pdfOptions->setIsRemoteEnabled(true); // Permettre de charger des ressources externes (ex: images)

        // Initialisez DomPDF avec les options définies
        $dompdf = new \Dompdf\Dompdf($pdfOptions);

        // Étape 2 : Récupérez les équipements depuis le repository
        $equipements = $equipementRepository->findAll();

        // Vérifiez si la liste est vide
        if (empty($equipements)) {
            throw $this->createNotFoundException('Aucun équipement trouvé pour générer le PDF.');
        }

        // Étape 3 : Préparez le contenu HTML (à partir d'un fichier Twig)
        $html = $this->renderView('backtemplates/pdf.html.twig', [ // Chemin mis à jour pour votre structure
            'equipements' => $equipements,
        ]);

        // Étape 4 : Chargez le contenu HTML dans DomPDF
        $dompdf->loadHtml($html);

        // Optionnel : Configurer la taille de papier et l'orientation (A4, Portrait)
        $dompdf->setPaper('A4', 'portrait');

        // Étape 5 : Générez le fichier PDF
        $dompdf->render();

        // Étape 6 : Retourner le fichier PDF comme réponse HTTP
        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="equipements.pdf"', // Le filename peut être personnalisé ici
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

        // Vérifiez si l'équipement existe
        if (!$equipement) {
            throw $this->createNotFoundException('L\'équipement n\'existe pas.');
        }

        // Stockez le nom de l'image existante au cas où aucun fichier n'est rechargé
        $originalImage = $equipement->getImage();

        // Créez et gérez le formulaire
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le fichier d'image
            $imageFile = $form->get('image')->getData(); // Champs de type 'file' dans le formulaire

            if ($imageFile) {
                // Nettoyez et sécurisez le nouveau nom de fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacez le nouveau fichier dans le répertoire configuré
                $imageFile->move(
                    $this->getParameter('equipements_directory'),
                    $newFilename
                );

                // Mettre à jour l'entité avec le nouveau chemin de fichier
                $equipement->setImage($newFilename);

                // Supprimer le précédent fichier d'image du serveur (optionnel mais recommandé)
                if ($originalImage) {
                    $oldImagePath = $this->getParameter('equipements_directory') . '/' . $originalImage;

                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            } else {
                // Si aucune nouvelle image n'est téléchargée, conserver l'ancienne image
                $equipement->setImage($originalImage);
            }

            // Sauvegarder les modifications en base de données
            $entityManager->flush();

            // Ajouter un message flash pour confirmer la modification
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
