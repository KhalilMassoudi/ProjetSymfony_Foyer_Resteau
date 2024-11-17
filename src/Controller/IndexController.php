<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('backtemplates/app-login.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('backtemplates/app_register.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/back', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('backtemplates/baseback.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/back2', name: 'app_index2')]
    public function index2(): Response
    {
        return $this->render('backtemplates/baseback2.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/back/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('backtemplates/app_profile.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/back/calander', name: 'app_calander')]
    public function calander(): Response
    {
        return $this->render('backtemplates/app_calander.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/front', name: 'app_front')]
    public function front(): Response
    {
        return $this->render('fronttemplates/basefront.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
    #[Route('/GestionServices', name: 'app_GestionServices')]
    public function gestionservice(): Response
    {
        return $this->render('backtemplates/GestionServices.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

}
