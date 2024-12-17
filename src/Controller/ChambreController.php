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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class ChambreController extends AbstractController
{
    #[Route("/app_chamber", name: "app_chamber")]
    #[Route("/chambre", name: "legacy_chamber")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        ReservationRepository $reservationRepository,
        SluggerInterface $slugger
    ): Response {
        // Création d'une nouvelle instance de Chambre
        $chambre = new Chambre();

        // Création et gestion du formulaire
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        // Soumission et validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du téléchargement de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    // Déplacement de l'image téléversée
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                    // Définir le nom de l'image dans l'entité
                    $chambre->setImage($newFilename);
                } catch (\Exception $e) {
                    // Gestion des erreurs (image)
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_chamber');
                }
            }

            // Persister la nouvelle chambre
            $entityManager->persist($chambre);
            $entityManager->flush();

            $this->addFlash('success', 'La chambre a été ajoutée avec succès.');

            // Redirection pour éviter un double envoi du formulaire
            return $this->redirectToRoute('app_chamber');
        }

        // Récupération des chambres et des réservations pour l'affichage
        $chambres = $chambreRepository->findAll();
        $reservations = $reservationRepository->findAll();

        // Rendu de la vue
        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
            'reservations' => $reservations,
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
        ReservationRepository $reservationRepository, // Le repository des réservations
        MailerInterface $mailer, // Service MailerInterface pour envoyer les emails
        UserInterface $user // L'utilisateur connecté
    ): Response {

        // Vérifiez que l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour effectuer une réservation.');
        }

        // Vérifiez si l'utilisateur a déjà effectué une réservation
        $existingReservation = $reservationRepository->findOneBy(['user' => $user]);

        if ($existingReservation) {
            $this->addFlash('warning', 'Vous pouvez réserver une chambre qu\'une seule fois.');
            return $this->redirectToRoute('app_front_chambre');
        }

        // Récupérez la chambre demandée
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre demandée n\'existe pas.');
        }

        // Créez une instance de réservation
        $reservation = new Reservation();
        $reservation->setChambre($chambre);
        $reservation->setUser($user);

        // Créez un formulaire basé sur l'entité Reservation
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Définir la date de réservation comme celle du moment
            $reservation->setDateReservation(new \DateTime());

            // Persist et enregistrez en base de données
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Préparez et envoyez un email de confirmation
            $email = (new Email())
                ->from('mbechir643@gmail.com') // Adresse de l'expéditeur
                ->to($user->getEmail()) // Email de l'utilisateur connecté
                ->subject('Confirmation de votre réservation')
                ->html($this->renderView('emails/reservation_confirmation.html.twig', [
                    'user' => $user,
                    'reservation' => $reservation,
                    'chambre' => $chambre,
                ]));

            $mailer->send($email);

            // Ajoutez un message flash et redirigez l'utilisateur
            $this->addFlash('success', 'Réservation effectuée avec succès ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('app_front_chambre');
        }

        // Si le formulaire n'est pas valide, ou pas encore soumis, affichez le formulaire
        return $this->render('fronttemplates/reservation_form.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }
    #[Route('/back/notifications', name: 'app_notifications', methods: ['GET'])]
    public function notifications(ReservationRepository $reservationRepository): JsonResponse
    {
        // Récupérer uniquement les réservations en attente grâce à la méthode du repository
        $reservations = $reservationRepository->findPendingReservations();

        // Transformer les données en JSON pour l'envoyer à la vue
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'nomEtudiant' => $reservation->getEtudiant()->getNom(), // Accès à l'entité étudiant pour récupérer le nom
                'dateReservation' => $reservation->getDateReservation()->format('d-m-Y H:i'), // Format date/heure
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
        // Récupérer la réservation via l'ID
        $reservation = $reservationRepository->find($id);

        // Vérifier si la réservation existe
        if (!$reservation) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Réservation non trouvée.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Récupérer la chambre associée à la réservation
        $chambre = $reservation->getChambre();
        if (!$chambre) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Chambre associée à la réservation introuvable.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Modifier le statut de la chambre via ENUM
        $chambre->setStatutChB(ChambreStatut::OCCUPEE); // Remplacez par le bon statut ENUM

        // Modifier le statut de la réservation
        $reservation->setStatut('Accepté'); // Assurez-vous que le statut "Accepté" est conforme.

        // Sauvegarder en base
        $entityManager->flush();

        // Réponse JSON en cas de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'Réservation acceptée avec succès.',
            'id' => $id
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
                'message' => "Réservation non trouvée."
            ], Response::HTTP_NOT_FOUND);
        }

        // Laisser la chambre inchangée, modifier uniquement le statut de la réservation
        $reservation->setStatut('Rejeté'); // Statut "Rejeté" pour la réservation

        // Enregistrer les modifications à la base de données
        $entityManager->flush();

        // Retourner une réponse JSON avec succès
        return new JsonResponse([
            'success' => true,
            'message' => "Réservation rejetée avec succès.",
            'id' => $id // Identifiant pour retirer cette notification côté client
        ], Response::HTTP_OK);
    }
}