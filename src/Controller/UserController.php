<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;



class UserController extends AbstractController
{
    /*#[Route('/', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('backtemplates/app-login.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }*/
    #[Route('/', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('backtemplates/app-login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }



    /* #[Route('/register', name: 'app_register')]
     public function register(): Response
     {
         return $this->render('backtemplates/app_register.html.twig', [
             'controller_name' => 'IndexController',
         ]);
     }*/


    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        //recuperer les donner du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            // Ne pas encoder le mot de passe, juste le mettre tel quel
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            // Sauvegarder l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // Rediriger après l'enregistrement
            $this->addFlash('success', 'Account created successfully!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('backtemplates/app_register.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/login/check', name: 'app_login_check', methods: ['POST'])]
    public function checkLogin(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Rechercher l'utilisateur par email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && $this->passwordHasher->isPasswordValid($user, $password)) {
            // Stocker l'information de connexion dans la session
            $session = $request->getSession();
            $session->set('user_id', $user->getId());

            // Vérifier le rôle de l'utilisateur et rediriger
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                // Si l'utilisateur est un admin, rediriger vers la page d'administration
                return $this->render('backtemplates/baseback.html.twig', [
                    'controller_name' => 'UserController',
                ]);
            } else {
                // Si l'utilisateur est un simple utilisateur, rediriger vers la page front-end
                return $this->render('fronttemplates/basefront.html.twig', [
                    'controller_name' => 'UserController',
                ]);
            }
        } else {
            // Afficher un message d'erreur si les identifiants sont incorrects
            $this->addFlash('error', 'Incorrect email or password');

            return $this->redirectToRoute('app_login');
        }
    }
    #[Route('/back2', name: 'app_index2')]
    public function listUsers(): Response
    {
        // Récupérer tous les utilisateurs
        $users = $this->entityManager->getRepository(User::class)->findAll();

        // Renvoyer les utilisateurs au template
        return $this->render('backtemplates/baseback2.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/back2/delete/{id}', name: 'admin_user_delete', methods: ['POST', 'GET'])]
    public function deleteUser(int $id): RedirectResponse
    {
        // Récupérer l'utilisateur par son ID
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            // Ajouter un message flash si l'utilisateur n'est pas trouvé
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_index2');
        }

        // Supprimer l'utilisateur
        $this->entityManager->remove($user);
        $this->entityManager->flush();//elle fait le commit dans la liste

        // Ajouter un message flash pour la confirmation de suppression
        $this->addFlash('success', 'User deleted successfully!');

        // Rediriger vers la liste des utilisateurs
        return $this->redirectToRoute('app_index2');
    }



    //aaqassee
    #[Route('/back', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('backtemplates/baseback.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /*#[Route('/back2', name: 'app_index2')]
    public function index2(): Response
    {
        return $this->render('backtemplates/baseback2.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }*/


    /*#[Route('/back/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('backtemplates/app_profile.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }*/


    #[Route('/back/calander', name: 'app_calander')]
    public function calander(): Response
    {
        return $this->render('backtemplates/app_calander.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/front', name: 'app_front')]
    public function front(): Response
    {
        return $this->render('fronttemplates/basefront.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    //update


    #[Route('/back2/update/{id}', name: 'admin_user_update', methods: ['GET', 'POST'])]
    public function updateUser(int $id, Request $request): Response
    {
        // Récupérer l'utilisateur par son ID
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            // Si l'utilisateur n'est pas trouvé, afficher un message flash et rediriger
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_index2');
        }

        // Créer le formulaire de mise à jour
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $this->entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'User successfully updated!');
            return $this->redirectToRoute('app_index2');
        }

        // Rendre le formulaire dans le template
        return $this->render('backtemplates/app_register.html.twig', [
            'form' => $form->createView(),

        ]);

    }


    #[Route('/back/profile', name: 'app_profile')]
    public function profile(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
        // Rediriger vers la page de connexion si non authentifié
        return $this->redirectToRoute('app_login');
    }

        return $this->render('backtemplates/app_profile.html.twig', [
            'user' => $user,
        ]);
    }


}
