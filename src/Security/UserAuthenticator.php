<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login'; // Route de connexion (définie dans SecurityController)

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        // Récupérer l'email à partir de la requête
        $email = $request->request->get('email');

        // Vérification si l'email est vide
        if (empty($email)) {
            throw new \InvalidArgumentException('Email cannot be null or empty.');
        }

        // Sauvegarder l'email dans la session (utile pour pré-remplir le champ email)
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Créer le Passport avec l'email et le mot de passe
        return new Passport(
            new UserBadge($email),  // L'email comme identifiant utilisateur
            new PasswordCredentials($request->request->get('password')),  // Le mot de passe
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),  // Vérification CSRF
                new RememberMeBadge(),  // Gérer "Se souvenir de moi"
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifier si une cible spécifique est définie
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Récupérer les rôles de l'utilisateur connecté
        $roles = $token->getRoleNames();

        // Redirection basée sur les rôles
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_index')); // Admin -> page backoffice
        } elseif (in_array('ROLE_USER', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('app_front')); // Utilisateur -> page front
        }

        // Par défaut, rediriger vers une page générique
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
