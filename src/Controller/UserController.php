<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DeamndeServiceRepository;
use App\Repository\DeamndePlatRepository;
use App\Repository\DemandePlatRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;



class UserController extends AbstractController
{

    #[Route('/', name: 'app_front')] 
public function front(Security $security): Response
{
    // Get the currently authenticated user
    $user = $security->getUser();

    // Pass the user to the template
    return $this->render('fronttemplates/basefront.html.twig', [
        'user' => $user,
    ]);
}


    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->createDefaultAdmin();
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // Récupérer les données du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Hacher le mot de passe
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);

                // Définir le rôle par défaut
                $user->setRoles(['ROLE_USER']);

                // Générer un token de vérification
                $verificationToken = bin2hex(random_bytes(32));
                $user->setVerificationToken($verificationToken);

                // Persister l'utilisateur dans la base de données
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Envoyer l'email de vérification
                $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@example.com', 'Your App Name'))
                    ->to($user->getEmail())
                    ->subject('Please verify your email address')
                    ->htmlTemplate('emails/verification_email.html.twig')
                    ->context([
                        'username' => $user->getUsername(),
                        'token' => $verificationToken,
                    ]);

                $mailer->send($email);

                // Message de confirmation
                $this->addFlash('success', 'Account created successfully! Please check your email to verify your account.');
                return $this->redirectToRoute('app_login');
            } else {
                // Si le formulaire est invalide (email en doublon ou autre erreur)
                $this->addFlash('error', 'Please fix the errors in the form. If you are using this email, try another one.');
            }
        }

        return $this->render('backtemplates/app_register.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    /*#[Route('/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    // Retrieve login errors, if any
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}


    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        // Symfony automatically handles session logout.
        // You don't need to manually clear the session here.

        // Add a flash message for logout success (optional)
        $this->addFlash('success', 'You have been logged out successfully.');

        // Symfony will handle redirection to the login page based on the security configuration
        return $this->redirectToRoute('app_login');
    }
*/

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
   /* #[Route('/back2', name: 'app_index2')]
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
// visuliser les demandes services + demande plat
    #[Route('/profile', name: 'app_user_profile')]
public function profileUser(DeamndeServiceRepository $demandeServiceRepository,DemandePlatRepository $demandePlatRepository,ReservationRepository  $reservationRepository): Response
{
    $user = $this->getUser();

    $demandes = $demandeServiceRepository->findByUser($user);
    $demandesPlats = $demandePlatRepository->findByUser($user);
    $reservations = $reservationRepository->findByUser($user);
    // Pass the demandes to the template
    return $this->render('fronttemplates/profile.html.twig', [
        'user' => $user,
        'demandes' => $demandes,
        'demandesPlats' => $demandesPlats,
        'reservations'=> $reservations
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

    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid or expired verification token.');
            return $this->redirectToRoute('app_login');
        }

        // Valider l'utilisateur
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $entityManager->flush();

        $this->addFlash('success', 'Your email has been verified successfully.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/active-users', name: 'app_active_users')]
    public function mostActiveUsers(UserRepository $userRepository): Response
    {
        $activeUsers = $userRepository->findMostActiveUsersByLogin();

        return $this->render('backtemplates/most_active_users.html.twig', [
            'activeUsers' => $activeUsers,
        ]);
    }

    #[Route('/back2/add', name: 'admin_user_add')]
    public function addUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Définir un rôle par défaut
            $user->setRoles(['ROLE_USER']);

            // Sauvegarder dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Étudiant ajouté avec succès!');

            return $this->redirectToRoute('app_index2'); // Retour à la liste des étudiants
        }

        return $this->render('backtemplates/add_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
