<?php

namespace App\Controller;

use App\Repository\DemandeServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
    public function listDeamndes(DemandeServiceRepository $rep): Response
    {   $demandes=$rep->findAll();
        return $this->render('service/demande/Demandes_back.html.twig', [
            'demandes' => $demandes ,
        ]);
    }
}
