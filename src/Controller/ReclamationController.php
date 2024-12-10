<?php

namespace App\Controller;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;

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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;





class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
    private $entityManager;

    // Injecter l'EntityManager via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/adminafficherreclamations', name: 'app_adminafficherReclamations')]
    public function adminafficherReclamation(Request $request, ReclamationRepository $rep, PaginatorInterface $paginator): Response
    {
        $limit = 3;

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
                ->find($reclamation->getUserId());
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




    #[Route('//afficherreclamations', name: 'app_afficherReclamations')]
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
        $pagination = $paginator->paginate(
            $rep->findAll(),  // La requête de base
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
        ]);
        // Renvoyer la vue avec la pagination

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





    #[Route('/ajoutReclamation', name: 'app_ajouterReclamation')]
    // Ajouter une réclamation
    public function ajouterReclamation(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        // Créer une nouvelle instance de Reclamation
        $reclamation = new Reclamation();

        // Créer le formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();

            // Assigner un utilisateur par défaut (ou récupérer l'utilisateur connecté si nécessaire)
            $reclamation->setUserId(2); // À modifier selon vos besoins

            // Récupérer l'image téléchargée et la traiter
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if (!$imageFile) {
                throw new \Exception('Le champ image n’a pas reçu de fichier.');
            }
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


    #[Route('/modifierReclamation/{id}', name: 'app_modifierReclamation')]
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
    #[Route('/supprimerReclamation/{id}', name: 'app_supprimerReclamation', methods: ['POST', 'GET'])]
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
    #[Route('/modifierReclamationn/{id}', name: 'app_modifierReclamationn', methods: ['GET', 'POST'])]
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



    #[Route('/ajouterReclamationn', name: 'app_ajouterReclamationn', methods: ['POST'])]
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

    #[Route('/reclamation/repondre/{id}', name: 'app_reclamation_repondre')]
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


    #[Route('/update-rating', name: 'update_rating', methods: ['POST'])]

    public function updateRating(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données envoyées par l'AJAX (ID de la réclamation et la nouvelle note)
        $data = json_decode($request->getContent(), true);

        // Vérifier que les paramètres nécessaires sont présents dans la requête
        $reclamationId = $data['reclamation_id'] ?? null;
        $newRating = $data['rating'] ?? null;

        if ($reclamationId === null || $newRating === null) {
            return new JsonResponse(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Trouver la réclamation dans la base de données
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);

        if (!$reclamation) {
            return new JsonResponse(['error' => 'Reclamation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Mettre à jour la note
        $reclamation->setRating($newRating);

        // Sauvegarder les changements
        $entityManager->flush();

        return new JsonResponse(['success' => 'Rating updated successfully'], JsonResponse::HTTP_OK);
    }
    #[Route('/ajouter-favori/{id}', name: 'app_ajouter_favori')]
    public function ajouterAuxFavoris(Reclamation $reclamation): Response
    {
        // Logique pour ajouter la réclamation aux favoris (ex. enregistrement dans une base de données)
    }


}