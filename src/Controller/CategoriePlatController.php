<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriePlatController extends AbstractController
{
    #[Route('/categorie/plat', name: 'app_categorie_plat')]
    public function index(): Response
    {
        return $this->render('categorie_plat/index.html.twig', [
            'controller_name' => 'CategoriePlatController',
        ]);
    }
}
