<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function AfficherAllServices(ServiceRepository $rep, Request $request, EntityManagerInterface $em): Response
    {
        $services = $rep->findAll();
        
        // Create a new Service for the form
        $service = new Service();
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);
    
        // If form is submitted, save the new service
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();
            
            return $this->redirectToRoute('app_service'); // Redirect after adding service
        }
    
        return $this->render('backtemplates/GestionServices.html.twig', [
            'service' => $services, // List of services
            'form' => $form->createView(), // The form for adding a new service
        ]);
    }
    
}

