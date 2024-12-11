<?php
namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Enum\ChambreStatut;
use App\Repository\ChambreRepository;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactoryInterface;
class ChambreController extends AbstractController
{
    #[Route("/chambre", name: "app_chamber")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        ReservationRepository $reservationRepository, // Ajout du repository des réservations
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
        // Récupérer les dernières réservations
        $reservations = $reservationRepository->findBy([], ['dateReservation' => 'DESC'], 5);

        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
            'reservations' => $reservations, // Passer les réservations à la vue
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
    #[Route("/front/chambre/reserver/{id}", name: "app_reserver_chambre")]
    public function reserver(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        ReservationRepository $reservationRepository, // Ajout du repository des réservations
        UserInterface $user // Injection de l'utilisateur connecté
    ): Response {

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour effectuer une réservation.');
        }

        // Vérifier si l'utilisateur a déjà une réservation
        $existingReservation = $reservationRepository->findOneBy(['user' => $user]);

        if ($existingReservation) {
            // Si l'utilisateur a déjà une réservation, afficher un message d'alerte et rediriger
            $this->addFlash('warning', 'Vous pouvez réserver une chambre qu\'une seule fois.');
            return $this->redirectToRoute('app_front_chambre'); // Redirige vers la page des chambres
        }

        // Récupérer la chambre demandée
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre demandée n\'existe pas.');
        }

        // Créer une nouvelle réservation
        $reservation = new Reservation();
        $reservation->setChambre($chambre);
        $reservation->setUser($user); // Associer la réservation à l'utilisateur connecté

        // Créer un formulaire de réservation
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, enregistrer la réservation
        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setDateReservation(new \DateTime()); // Ajouter une date de réservation
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès!');
            return $this->redirectToRoute('app_front_chambre');
        }

        // Passer l'entité Reservation à la vue
        return $this->render('fronttemplates/reservation_form.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }
    #[Route('/back/notifications', name: 'app_notifications')]
    public function notifications(ReservationRepository $reservationRepository): JsonResponse
    {
        // Récupérer les réservations récentes (par exemple, les 5 dernières)
        $reservations = $reservationRepository->findBy([], ['dateReservation' => 'DESC'], 5);

        // Transformer les données en JSON pour l'envoyer à la vue
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'nomEtudiant' => $reservation->getNomEtudiant(),
                'dateReservation' => $reservation->getDateReservation()->format('d-m-Y H:i'),
            ];
        }

        return new JsonResponse($data);
    }
    #[Route('/back/notifications/accepter/{id}', name: 'app_accepter_reservation', methods: ['POST'])]
    public function accepterReservation(
        int $id,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupération de la réservation spécifique via son ID
        $reservation = $reservationRepository->find($id);

        // Vérifier si la réservation existe
        if (!$reservation) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Réservation non trouvée.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Mise à jour : statut de la chambre associé à "Occupée"
        $chambre = $reservation->getChambre();
        if (!$chambre) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Chambre associée à la réservation introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        $chambre->setStatutChB(ChambreStatut::OCCUPEE);
        // Enregistrer les modifications
        $entityManager->flush();

        // Retourner une réponse JSON au succès
        return new JsonResponse([
            'success' => true,
            'message' => 'Réservation acceptée avec succès.',
            'id' => $id // Utile pour identifier la réservation à supprimer côté client
        ], Response::HTTP_OK);
    }
    #[Route('/back/notifications/rejeter/{id}', name: 'app_rejeter_reservation', methods: ['POST'])]
    public function rejeterReservation(
        int $id,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupérer la réservation par ID
        $reservation = $reservationRepository->find($id);

        // Vérifier si la réservation existe
        if (!$reservation) {
            return new JsonResponse([
                'success' => false,
                'message' => 'La réservation spécifiée est introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Supprimer la réservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        // Retourner une réponse JSON indiquant le succès
        return new JsonResponse([
            'success' => true,
            'message' => 'Réservation rejetée et supprimée avec succès.',
            'id' => $id // Utile pour identifier la réservation supprimée côté client
        ], Response::HTTP_OK);
    }
}
