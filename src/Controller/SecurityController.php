<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security; // Importer le service Security

class SecurityController extends AbstractController
{
    /*#[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // Si l'utilisateur est déjà authentifié, redirigez vers la page d'accueil
        if ($this->getUser()) {
            // Récupérer le token de sécurité pour déterminer les rôles de l'utilisateur
            $token = $security->getToken();
            $roles = $token ? $token->getRoleNames() : [];

            // Rediriger en fonction des rôles de l'utilisateur
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('app_index2'); // Page admin
            } elseif (in_array('ROLE_USER', $roles)) {
                return $this->redirectToRoute('app_front'); // Page front
            }

            return $this->redirectToRoute('app_login'); // Redirection par défaut
        }

        // Récupérer les erreurs de connexion et le dernier nom d'utilisateur saisi
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('backtemplates/app-login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }*/

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
