<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function AfficherAllServices(ServiceRepository $rep): Response
    {   $services=$rep->findAll();
        return $this->render('backtemplates/GestionServices.html.twig', [
            'service' => $services ,
        ]);
    }
}
