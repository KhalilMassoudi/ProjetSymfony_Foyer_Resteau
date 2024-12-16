<?php

namespace App\Controller;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Service\MailService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;





class ReclamationController extends AbstractController
{
    #[Route('/dashreclamation', name: 'app_reclamation')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findBy([], ['date_Reclamation' => 'DESC'], 7);

        // Calculer le temps écoulé pour chaque réclamation
        $reclamationsWithTime = [];
        foreach ($reclamations as $reclamation) {
            $timeAgo = $this->timeAgo($reclamation->getDateReclamation());
            $reclamationsWithTime[] = [
                'reclamation' => $reclamation,
                'timeAgo' => $timeAgo,
            ];
        }

        $totalReclamations = $reclamationRepository->countTotalReclamations();
        $totalReclamationsRepondues = $reclamationRepository->countReclamationsRepondues();
        $totalFeedbacks = $reclamationRepository->countFeedbacks();
        $percentageRepondues = 0;
        if ($totalReclamations > 0) {
            $percentageRepondues = ($totalReclamationsRepondues / $totalReclamations) * 100;
        }
        $negativeRatingsCount = $reclamationRepository->countNegativeRatings();
        $positiveRatingsCount = $reclamationRepository->countPositiveRatings();
        // Récupérer le nombre total de réclamations ayant un rating différent de 0
        $totalReclamations = $reclamationRepository->count([]);
        $typesReclamationss = $reclamationRepository->findTypesAndCounts();

        // Calcul des pourcentages pour chaque type
        $pourcentages = [];
        foreach ($typesReclamationss as $type => $count) {
            $pourcentages[$type] = ($totalReclamations > 0) ? round(($count / $totalReclamations) * 100, 2) : 0;
        }
        $percentageFeedback = $totalReclamations > 0 ? ($totalFeedbacks / $totalReclamations) * 100 : 0;
        $percentageNonRepondues = $totalReclamations > 0 ? (($totalReclamations - $totalReclamationsRepondues) / $totalReclamations) * 100 : 0;
        $percentagePositiveRatings = $totalFeedbacks > 0 ? ($positiveRatingsCount / $totalFeedbacks) * 100 : 0;
        $percentageNegativeRatings = $totalFeedbacks > 0 ? ($negativeRatingsCount / $totalFeedbacks) * 100 : 0;

        return $this->render('backtemplates/app-reclamationdash.html.twig', [
            'controller_name' => 'ReclamationController',
            'total_reclamations' => $totalReclamations,
            'total_reclamations_repondues' => $totalReclamationsRepondues,
            'total_feedbacks' => $totalFeedbacks,
            'percentage_repondues' => $percentageRepondues,
            'percentageFeedback' => $percentageFeedback,
            'percentageNonRepondues' => $percentageNonRepondues,
            'percentage_positive_ratings' => $percentagePositiveRatings,
            'percentage_negative_ratings' => $percentageNegativeRatings,
            'reclamations' => $reclamationsWithTime,

            'pourcentages' => $pourcentages
        ]);
    }
    private $entityManager;

    // Injecter l'EntityManager via le constructeur

    #[Route('/adminafficherreclamations', name: 'app_adminafficherReclamations')]
    public function adminafficherReclamation(Request $request, ReclamationRepository $rep, PaginatorInterface $paginator): Response
    {
        $limit = 5;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant

        // Récupérer les réclamations avec la pagination et le tri
        $reclamations = $rep->findBy([], [$sortField => $sortDirection]);

        // Pagination
        $pagination = $paginator->paginate(
            $reclamations,  // La requête de réclamations triées
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit  // Nombre d'éléments par page
        );

        // Récupérer les utilisateurs associés aux réclamations
        $users = [];
        foreach ($reclamations as $reclamation) {
            // Récupérer l'utilisateur associé à la réclamation en utilisant user_id
            $users[$reclamation->getId()] = $this->entityManager
                ->getRepository(User::class)
                ->find($reclamation->getUser());
        }

        // Créer le formulaire de modification
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer la vue avec la pagination, les réclamations et le formulaire
        return $this->render('backtemplates/backreclamations.html.twig', [
            'reclamations' => $pagination,  // Utilisation de la pagination
            'formModif' => $formModif->createView(),
            'pagination' => $pagination,
            'users' => $users
        ]);
    }

    #[Route('/adminafficherreclamationsrepondues', name: 'app_adminafficherReclamationsrepondues')]
    public function reclamationsRepondue(): Response
    {
        $reclamations = $this->reclamationRepository->findReclamationsRepondue();

        return $this->render('backtemplates/backReclamationsnonrepondues.html.twig', [
            'reclamations' => $reclamations
        ]);
    }
    #[Route('/adminafficherreclamationsuspendues', name: 'app_adminafficherReclamationssuspendues')]

    public function adminafficherReclamationsuspenduesondues(Request $request, ReclamationRepository $rep, PaginatorInterface $paginator): Response
    {
        $limit = 3;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant

        // Récupérer les réclamations avec la pagination et le tri
        $reclamations = $rep->findReclamationsSuspendue([], [$sortField => $sortDirection]);

        // Pagination
        $pagination = $paginator->paginate(
            $reclamations,  // La requête de réclamations triées
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit  // Nombre d'éléments par page
        );

        // Récupérer les utilisateurs associés aux réclamations
        $users = [];
        foreach ($reclamations as $reclamation) {
            // Récupérer l'utilisateur associé à la réclamation en utilisant user_id
            $users[$reclamation->getId()] = $this->entityManager
                ->getRepository(User::class)
                ->find($reclamation->getUser());
        }

        // Créer le formulaire de modification
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer la vue avec la pagination, les réclamations et le formulaire
        return $this->render('backtemplates/backReclamationsnonrepondues.html.twig', [
            'reclamations' => $pagination,  // Utilisation de la pagination
            'formModif' => $formModif->createView(),
            'pagination' => $pagination,
            'users' => $users,

        ]);
    }
    #[Route('/reclamations/afficherreclamationsrépondues', name: 'app_afficherReclamationsrpondues')]
    public function adminafficherReclamationrepondues(Request $request, ReclamationRepository $rep, PaginatorInterface $paginator): Response
    {
        $limit = 3;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant

        // Récupérer les réclamations avec la pagination et le tri
        $reclamations = $rep->findReclamationsRepondue([], [$sortField => $sortDirection]);

        // Pagination
        $pagination = $paginator->paginate(
            $reclamations,  // La requête de réclamations triées
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit  // Nombre d'éléments par page
        );

        // Récupérer les utilisateurs associés aux réclamations
        $users = [];
        foreach ($reclamations as $reclamation) {
            // Récupérer l'utilisateur associé à la réclamation en utilisant user_id
            $users[$reclamation->getId()] = $this->entityManager
                ->getRepository(User::class)
                ->find($reclamation->getUser());
        }

        // Créer le formulaire de modification
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer la vue avec la pagination, les réclamations et le formulaire
        return $this->render('backtemplates/backReclamationsrepondues.html.twig', [
            'reclamations' => $pagination,  // Utilisation de la pagination
            'formModif' => $formModif->createView(),
            'pagination' => $pagination,
            'users' => $users,

        ]);
    }
    #[Route('/reclamations/afficherreclamations', name: 'app_afficherReclamations')]
    public function afficherReclamation(ReclamationRepository $rep, Request $request, PaginatorInterface $paginator): Response
    {
        // Nombre d'éléments par page
        // Nombre d'éléments par page
        $limit = 3;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant
        $formModif = $this->createForm(ReclamationType::class);
        // Pagination
         $user = $this->getUser();
         $userId = $user->getId();
        $nombreFavoris = $rep->countFavorisByUser($userId);
        $pagination = $paginator->paginate(
            $rep->findByUser($userId),
            // La requête de base
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit,  // Nombre d'éléments par page
            [
                'sortField' => $sortField,  // Champ par défaut pour trier
                'sortDirection' => $sortDirection,  // Direction par défaut
            ]
        );
        return $this->render('reclamation/afficherReclamations.html.twig', [
            'reclamations' => $rep->findAll(),
            'formModif' => $formModif->createView(),
            'formAjout' => $formModif->createView(),
            'pagination' => $pagination,
            'nombreFavoris'=>$nombreFavoris
        ]);
        // Renvoyer la vue avec la pagination

    }
    #[Route('/reclamations/frontafficheafficherreclamationsrepondues', name: 'app_frontafficherReclamationsrepondues')]
    public function afficherReclamationrepondues(
        ReclamationRepository $rep,
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ): Response {
        // Nombre d'éléments par page
        $limit = 3;
        $user = $this->getUser();
        $userId = $user->getId();
        $nombreFavoris = $rep->countFavorisByUser($userId);
        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'date_Reclamation'); // Par défaut, trier par 'date_Reclamation'
        $sortDirection = $request->query->get('sort_direction', 'DESC'); // Par défaut, ordre descendant

        // Obtenir l'utilisateur connecté
        $user = $this->getUser();

        // Créer un formulaire pour ajouter une nouvelle réclamation
        $newReclamation = new Reclamation();
        $formAjout = $this->createForm(ReclamationType::class, $newReclamation);
        $formAjout->handleRequest($request);

        // Traiter le formulaire
        if ($formAjout->isSubmitted() && $formAjout->isValid()) {
            $newReclamation->setUser($user); // Associer l'utilisateur connecté
            $newReclamation->setEtat('en attente'); // État par défaut
            $newReclamation->setDateReclamation(new \DateTime()); // Ajouter la date actuelle

            $entityManager->persist($newReclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été ajoutée avec succès.');

            return $this->redirectToRoute('reclamations_repondues'); // Redirection après soumission
        }

        // Créer une requête Doctrine QueryBuilder
        $queryBuilder = $rep->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->andWhere('r.etat = :etat')
            ->setParameter('userId', $user)
            ->setParameter('etat', 'répondue')
            ->orderBy("r.$sortField", $sortDirection);

        // Ajouter la pagination
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(), // Utiliser la requête générée
            $request->query->getInt('page', 1), // La page actuelle (1 par défaut)
            $limit // Nombre d'éléments par page
        );

        return $this->render('reclamation/reclamationsrepondues.html.twig', [
            'pagination' => $pagination, // Contient les réclamations paginées
            'formAjout' => $formAjout->createView(),
            'nombreFavoris'=>$nombreFavoris// Vue du formulaire pour l'ajout
        ]);
    }


    #[Route('/backreclamationdetails/{id}', name: 'app_reclamation_details', methods: ['GET'])]
    public function details(int $id, ManagerRegistry $doctrine): Response
    {
        // Récupérer la réclamation par son ID
        $reclamation = $doctrine->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            throw $this->createNotFoundException("La réclamation avec l'ID $id n'existe pas.");
        }

        // Retourner la vue avec les détails de la réclamation
        return $this->render('backtemplates/backreclamationdetails.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }





    #[Route('/reclamations/ajoutReclamation', name: 'app_ajouterReclamation')]
    // Ajouter une réclamation
    public function ajouterReclamation(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        // Créer une nouvelle instance de Reclamation
        $reclamation = new Reclamation();
        $user = $this->getUser();
        // Créer le formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            // Assigner un utilisateur par défaut (ou récupérer l'utilisateur connecté si nécessaire)
            $reclamation->setUser($user); // À modifier selon vos besoins

            // Récupérer l'image téléchargée et la traiter
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Créer un nom unique pour le fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    // Déplacer le fichier dans le répertoire d'upload
                    $imageFile->move(
                        $this->getParameter('reclamations_directory'), // Répertoire d'upload défini dans services.yaml
                        $newFilename
                    );
                    // Assigner le nom du fichier à l'entité Reclamation
                    $reclamation->setImage($newFilename);
                } catch (FileException $e) {
                    // En cas d'erreur, ajouter un message flash
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_ajouterReclamation');
                }
            }

            // Enregistrer l'entité dans la base de données
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Réclamation ajoutée avec succès !');

            // Rediriger vers la liste des réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Afficher le formulaire dans la vue
        return $this->render('reclamation/ajoutReclamation.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/reclamations/modifierReclamation/{id}', name: 'app_modifierReclamation')]
    public function modifierReclamation(int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        // Récupération de l'Entity Manager
        $em = $doctrine->getManager();

        // Récupération de la réclamation à modifier
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Si la réclamation n'existe pas, afficher une erreur
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Création du formulaire pré-rempli avec les données existantes
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Traitement des données saisies dans le formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Les données sont automatiquement mises à jour dans l'entité via le formulaire
            // Sauvegarde des modifications
            $em->flush();

            // Redirection vers la page d'affichage des réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('reclamation/ajoutReclamation.html.twig', [
            'form' => $form->createView(), // Passer la vue du formulaire à la vue Twig
        ]);
    }
    #[Route('/reclamations/supprimerReclamation/{id}', name: 'app_supprimerReclamation', methods: ['POST', 'GET'])]
    public function supprimerReclamation(ManagerRegistry $doctrine, int $id): Response
    {
        // Récupérer l'Entity Manager
        $em = $doctrine->getManager();

        // Récupérer la réclamation par son ID
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Vérifier si la réclamation existe
        if (!$reclamation) {
            $this->addFlash('error', 'Réclamation introuvable.');
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Supprimer la réclamation
        $em->remove($reclamation);
        $em->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Réclamation supprimée avec succès.');

        // Rediriger vers la liste des réclamations
        return $this->redirectToRoute('app_afficherReclamations');
    }
    #[Route('/reclamations/modifierReclamationn/{id}', name: 'app_modifierReclamationn', methods: ['GET', 'POST'])]
    public function modifierReclamationn($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        // Trouver la réclamation par son ID
        $reclamation = $em->getRepository(Reclamation::class)->find($id);

        // Si la réclamation n'existe pas, lancer une exception
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Créer le formulaire pré-rempli avec les données existantes
        $form = $this->createForm(ReclamationType::class, $reclamation);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la réclamation mise à jour
            $em->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Réclamation modifiée avec succès.');

            // Rediriger vers la page qui liste les réclamations
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // Renvoyer la page avec le formulaire
        return $this->render('reclamation/afficherReclamations.html.twig', [
            'formModif' => $form->createView(),  // Passer le formulaire au template
        ]);
    }



    #[Route('/reclamations/ajouterReclamationn', name: 'app_ajouterReclamationn', methods: ['POST'])]
    public function ajouterReclamationn(ManagerRegistry $doctrine, Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($reclamation);  // Persist the new reclamation
            $em->flush();  // Save it to the database

            // Flash message for success
            $this->addFlash('success', 'Réclamation ajoutée avec succès.');

            // Redirect to the list of reclamations or stay on the current page
            return $this->redirectToRoute('app_afficherReclamations');
        }

        // If form is not valid, display the form with error messages
        $this->addFlash('error', 'Erreur lors de l\'ajout de la réclamation.');

        // Re-render the same page
        return $this->redirectToRoute('app_afficherReclamations');
    }

    #[Route('/reclamations/repondre/{id}', name: 'app_reclamation_repondre')]
    public function repondreReclamation(
        int $id,
        Request $request,
        ReclamationRepository $rep,
        ManagerRegistry $doctrine
    ): Response {
        // Récupérer la réclamation par ID
        $reclamation = $rep->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable.');
        }

        // Créer un formulaire pour la réponse (seulement le champ "reponse")
        $form = $this->createFormBuilder($reclamation)
            ->add('reponse', TextareaType::class, [
                'label' => 'Votre réponse',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->getForm();

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la réponse dans la base de données
            $em = $doctrine->getManager();
            $em->persist($reclamation);
            $em->flush();

            // Ajouter un message flash et rediriger vers la liste des réclamations
            $this->addFlash('success', 'Réponse envoyée avec succès.');
            return $this->redirectToRoute('app_adminafficherReclamations');
        }

        // Renvoyer le formulaire de réponse à la vue
        return $this->render('backtemplates/repondreReclamation.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }



    #[Route('/reclamations/update-rating', name: 'update_rating', methods: ['POST'])]
    public function updateRating(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        // Récupérer les données envoyées par l'AJAX (ID de la réclamation et la nouvelle note)
        $data = json_decode($request->getContent(), true);
        $logger->info('Data received:', $data); // Log des données reçues

        // Vérifier que les paramètres nécessaires sont présents dans la requête
        $reclamationId = $data['reclamation_id'] ?? null;
        $newRating = $data['rating'] ?? null;

        if ($reclamationId === null || $newRating === null) {
            $logger->error('Invalid data: Missing reclamation_id or rating');
            return new JsonResponse(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Trouver la réclamation dans la base de données
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);

        if (!$reclamation) {
            $logger->error('Reclamation not found: ' . $reclamationId);
            return new JsonResponse(['error' => 'Reclamation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Mettre à jour la note
        $reclamation->setRating($newRating);
        $logger->info('Updating rating for reclamation ID: ' . $reclamationId . ' to ' . $newRating);

        // Sauvegarder les changements
        try {
            $entityManager->flush();
            $logger->info('Rating updated successfully for reclamation ID: ' . $reclamationId);
            return new JsonResponse(['success' => 'Rating updated successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $logger->error('Error updating rating for reclamation ID ' . $reclamationId . ': ' . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to update rating'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/reclamations/ajouter-favori/{id}', name: 'app_ajouter_favori')]
    public function toggleFavori(int $id, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer la réclamation par ID
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException("Réclamation introuvable.");
        }

        // Basculer l'état du favori
        $currentFavoriStatus = $reclamation->isFavori();
        $reclamation->setFavori(!$currentFavoriStatus);

        // Enregistrer dans la base de données
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        if (!$currentFavoriStatus) {
            $this->addFlash('success', 'Réclamation ajoutée aux favoris.');
        } else {
            $this->addFlash('info', 'Réclamation supprimée des favoris.');
        }

        // Rediriger vers la liste des réclamations
        return $this->redirectToRoute('app_afficherReclamations'); // Changez 'app_liste_reclamation' si nécessaire
    }

    private function timeAgo(\DateTime $dateTime): string
    {
        $now = new DateTime();
        $interval = $now->diff($dateTime);

        if ($interval->y > 0) {
            return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        }
        if ($interval->m > 0) {
            return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        }
        if ($interval->d > 0) {
            return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        }
        if ($interval->h > 0) {
            return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        }
        if ($interval->i > 0) {
            return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        }
        return 'just now';
    }


    #[Route('/reclamationsrepondrefromsuspendue/repondre/{id}', name: 'app_reclamation_repondrefromsuspendue')]
    public function repondrefromsuspendue(
        int $id,
        Request $request,
        ReclamationRepository $rep,
        ManagerRegistry $doctrine,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer la réclamation par ID
        $reclamation = $rep->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable.');
        }

        // Vérifier que la réclamation est suspendue avant de permettre une réponse
        if ($reclamation->getEtat() !== 'suspendue') {
            throw $this->createAccessDeniedException('Vous ne pouvez répondre qu\'aux réclamations suspendues.');
        }

        // Créer un formulaire pour la réponse (seulement le champ "reponse")
        $form = $this->createFormBuilder($reclamation)
            ->add('reponse', TextareaType::class, [
                'label' => 'Votre réponse',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->getForm();

        // Traiter le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Modifier l'état de la réclamation pour indiquer qu'elle a été répondue
            $reclamation->setEtat('répondue');

            // Sauvegarder la réponse dans la base de données
            $em = $doctrine->getManager();
            $em->persist($reclamation);
            $em->flush();

            // Ajouter un message flash et rediriger vers la liste des réclamations suspendues
            $this->addFlash('success', 'Réponse envoyée avec succès.');
            return $this->redirectToRoute('app_adminafficherReclamationssuspendues');
        }

        // Récupérer toutes les réclamations suspendues pour afficher la liste avec pagination
        $limit = 3;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');
        $sortDirection = $request->query->get('sort_direction', 'asc');

        $query = $rep->createQueryBuilder('r')
            ->where('r.etat = :etat')
            ->setParameter('etat', 'suspendue')
            ->orderBy('r.' . $sortField, $sortDirection)
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, // Requête paginée
            $request->query->getInt('page', 1), // Page actuelle
            $limit // Nombre d'éléments par page
        );


        // Récupérer les utilisateurs associés aux réclamations
        $users = [];
        foreach ($pagination as $reclamation) {
            $users[$reclamation->getId()] = $doctrine->getRepository(User::class)->find($reclamation->getUser());
        }

        // Créer un formulaire de modification pour l'affichage (facultatif)
        $formModif = $this->createForm(ReclamationType::class);

        // Renvoyer les données à la vue
        return $this->render('backtemplates/repondreReclamation.html.twig', [
            'reclamations' => $pagination, // Utilisation de la pagination
            'formModif' => $formModif->createView(),
            'pagination' => $pagination,
            'users' => $users, // Utilisateurs associés
            'form' => $form->createView(),
            'reclamation'=>$reclamation,// Formulaire de réponse
        ]);
    }
    private $mailService;

    public function __construct(EntityManagerInterface $entityManager,MailService $mailService)
    {
        $this->entityManager = $entityManager;
        $this->mailService = $mailService;
    }


    // src/Controller/ReclamationController.php



    #[Route('/reclamation/send-email', name: 'send_mail', methods: ['POST'])]
    public function sendMail(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer l'email de l'utilisateur
        $userEmail = $user ? $user->getEmail() : 'default@example.com'; // Assurez-vous d'avoir un email par défaut

        // Informations sur la réclamation
        $reclamationId = $request->get('reclamationId');

        // L'email à envoyer
        $adminEmail = $_ENV['MAIL_TO'] ?? null;// Adresse email à partir de la variable d'environnement

        // Sujet et contenu de l'email
        $subject = "Réclamation ID: $reclamationId";
        $content = "Voici les détails de la réclamation avec l'ID $reclamationId";

        try {
            $this->mailService->sendReclamationEmail($userEmail, $adminEmail, $subject, $content);
            return $this->json(['message' => 'Email envoyé avec succès !', 'reclamationID' => $reclamationId]);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            // Log l'exception pour mieux comprendre l'erreur
            $this->get('logger')->error('Erreur d\'envoi de l\'email: ' . $e->getMessage());
            return $this->json(['error' => 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Capturer toutes les autres exceptions
            return $this->json(['error' => 'Erreur inconnue : ' . $e->getMessage()], 500);
        }
    }
    #[Route('/adminreclamations/recherche', name: 'app_reclamation_search', methods: ['GET'])]
    public function search(Request $request, ReclamationRepository $repository, PaginatorInterface $paginator): Response
    {
        $limit = 5;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant

        // Récupérer les réclamations avec la pagination et le tri
        $titre = $request->query->get('title');
        $nomEtudiant = $request->query->get('studentName');
        $email = $request->query->get('email');
        $dateReclamation = $request->query->get('reclamationDate');

// Filtrer les champs vides
        $searchCriteria = array_filter([
            'titre' => $titre,
            'nomEtudiant' => $nomEtudiant,
            'email' => $email,
            'dateReclamation' => $dateReclamation,
        ]);
// Appeler la méthode de recherche avancée
        $reclamations = $repository->advancedSearch($searchCriteria);


        // Pagination
        $pagination = $paginator->paginate(
            $reclamations,  // La requête de réclamations triées
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit  // Nombre d'éléments par page
        );

        // Récupérer les utilisateurs associés aux réclamations
        $users = [];
        foreach ($reclamations as $reclamation) {
            // Récupérer l'utilisateur associé à la réclamation en utilisant user_id
            $users[$reclamation->getId()] = $this->entityManager
                ->getRepository(User::class)
                ->find($reclamation->getUser());
        }



// Rendre la vue avec les résultats
        return $this->render('backtemplates/recherchereclamation.html.twig', [
            'reclamations' => $reclamations,
            'users' => $users,
            'pagination' => $pagination
        ]);

    }

    #[Route('/favoris', name: 'app_voirfavoris')]
    public function voirFavoris(ReclamationRepository $reclamationRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $limit = 3;

        // Tri (optionnel)
        $sortField = $request->query->get('sort_field', 'titre');  // Par défaut, trier par 'titre'
        $sortDirection = $request->query->get('sort_direction', 'asc');  // Par défaut, ordre ascendant

        // Pagination
        $user = $this->getUser();
        $userId = $user->getId();

        // Récupération des favoris et du nombre de favoris pour l'utilisateur
        $nombreFavoris = $reclamationRepository->countFavorisByUser($userId);
        $favoris = $reclamationRepository->findFavorisByUser($userId);

        // Utilisation de la méthode findFavorisByUser pour la pagination
        $pagination = $paginator->paginate(
            $reclamationRepository->findFavorisByUser($userId), // Utilisation de findFavorisByUser ici
            $request->query->getInt('page', 1),  // La page actuelle, 1 par défaut
            $limit,  // Nombre d'éléments par page
            [
                'sortField' => $sortField,  // Champ par défaut pour trier
                'sortDirection' => $sortDirection,  // Direction par défaut
            ]
        );

        // Passer les favoris et le nombre à la vue
        return $this->render('reclamation/voirFavoris.html.twig', [
            'reclamations' => $pagination,  // Utilisation de la pagination ici
            'nombreFavoris' => $nombreFavoris,
            'pagination' => $pagination,
        ]);
    }
    #[Route('/reclamations/ajouter-favorii/{id}', name: 'app_ajouter_favorii')]
    public function toggleeFavori(int $id, EntityManagerInterface $entityManager, ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer la réclamation par ID
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException("Réclamation introuvable.");
        }

        // Basculer l'état du favori
        $currentFavoriStatus = $reclamation->isFavori();
        $reclamation->setFavori(!$currentFavoriStatus);

        // Enregistrer dans la base de données
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Ajouter un message flash pour informer l'utilisateur
        if (!$currentFavoriStatus) {
            $this->addFlash('success', 'Réclamation ajoutée aux favoris.');
        } else {
            $this->addFlash('info', 'Réclamation supprimée des favoris.');
        }

        // Rediriger vers la liste des réclamations
        return $this->redirectToRoute('app_voirfavoris'); // Changez 'app_liste_reclamation' si nécessaire
    }






}