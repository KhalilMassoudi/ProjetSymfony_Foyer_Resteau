<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // The login form route
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get the last username entered (for re-populating the form)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Redirect the user to the appropriate page after successful authentication
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony will intercept this route and handle the logout automatically
        // You can add any custom logout behavior here if needed
    }

    // Protects the routes based on roles (e.g., redirect after login)
    #[Route('/back', name: 'app_index')]
    public function index(Security $security): Response
    {
        // Check if the user is logged in and has the appropriate role
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('backtemplates/baseback.html.twig');
        }

        // If the user is not an admin, redirect to the login page
        return $this->redirectToRoute('app_login');
    }
}
