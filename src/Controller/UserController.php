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

    #[Route('/', name: 'app_front')]
    public function login(): Response
    {
        return $this->render('fronttemplates/basefront.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

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


            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_USER']);




            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Account created successfully!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('backtemplates/app_register.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/login/check', name: 'app_login_check')]
    public function checkLogin(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && $this->passwordHasher->isPasswordValid($user, $password)) {
            // Démarrer la session et stocker les informations utilisateur
            $session = $request->getSession();
            $session->set('user_id', $user->getId());
            $session->set('username', $user->getUsername()); // Assurez-vous que la méthode getUsername() existe

            // Vérifier le rôle de l'utilisateur
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->render('backtemplates/baseback.html.twig', [
                    'controller_name' => 'UserController',
                    'user_id' => $user->getId(),
                    'username' => $user->getUsername(),
                ]);
            } else {
                return $this->render('fronttemplates/home.html.twig', [
                    'controller_name' => 'UserController',
                    'user_id' => $user->getId(),
                    'username' => $user->getUsername(),
                ]);
            }
        } else {
            // Ajouter un message flash pour indiquer une erreur
            $this->addFlash('error', 'Incorrect email or password');

            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        // Récupérer la session
        $session = $request->getSession();

        // Supprimer toutes les données de la session
        $session->clear();

        // Ajouter un message flash pour confirmation
        $this->addFlash('success', 'Vous avez été déconnecté avec succès.');

        // Rediriger vers la page de login
        return $this->redirectToRoute('app_login');
    }


    #[Route('/back2', name: 'app_index2')]
    public function listUsers(): Response
    {

        $users = $this->entityManager->getRepository(User::class)->findAll();


        return $this->render('backtemplates/baseback2.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/back2/delete/{id}', name: 'admin_user_delete', methods: ['POST', 'GET'])]
    public function deleteUser(int $id): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_index2');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();//elle fait le commit dans la liste

        $this->addFlash('success', 'User deleted successfully!');

        return $this->redirectToRoute('app_index2');
    }




    #[Route('/back', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('backtemplates/baseback.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/back2', name: 'app_index2')]
    public function index2(): Response
    {
        return $this->render('backtemplates/baseback2.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }


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

    #[Route('/login', name: 'app_login')]
    public function front(): Response
    {
        return $this->render('backtemplates/app-login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    //update


    #[Route('/back2/update/{id}', name: 'admin_user_update', methods: ['GET', 'POST'])]
    public function updateUser(int $id, Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_index2');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications
            $this->entityManager->flush();

            $this->addFlash('success', 'User successfully updated!');
            return $this->redirectToRoute('app_index2');
        }

        return $this->render('backtemplates/app_register.html.twig', [
            'form' => $form->createView(),

        ]);

    }


    #[Route('/back/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
        return $this->redirectToRoute('app_login');
    }

        return $this->render('backtemplates/app_profile.html.twig', [
            'user' => $user,
        ]);
    }

    /////creation automatique de l'admin

    public function loginAdmin(): Response
    {
        $this->createDefaultAdmin();

        return $this->render('backtemplates/app-login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }




    #[Route('/create-admin', name: 'app_create_admin', methods: ['GET'])]

    private function createDefaultAdmin(): void
    {
        $adminEmail = 'admin@gmail.com';

        $adminExists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $adminEmail]);

        if (!$adminExists) {

            $admin = new User();
            $admin->setUsername('admin');
            $admin->setEmail('admin@gmail.com');
            $admin->setRoles(['ROLE_ADMIN']);

            $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
            $admin->setPassword($hashedPassword);

            $this->entityManager->persist($admin);
            $this->entityManager->flush();


        }
    }




}
