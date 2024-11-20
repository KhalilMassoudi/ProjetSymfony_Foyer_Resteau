<?php

namespace App\Controller;

use App\Entity\TypeService;
use App\Form\TypeServiceFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TypeServiceController extends AbstractController
{
    #[Route('/service/type', name: 'app_type_service')]
    public function AfficherAllTypes(TypeServiceRepository $rep, Request $request, EntityManagerInterface $em): Response
    {
        $types = $rep->findAll();
        
        // Create a new Service for the form
        $type = new TypeService();
        $form = $this->createForm(TypeServiceFormType::class, $type);
        $form->handleRequest($request);
    
        // If form is submitted, save the new service
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();
            
            return $this->redirectToRoute('app_type_service'); // Redirect after adding service
        }
    
        return $this->render('service/TypeService.html.twig', [
            'type' => $types, // List of types
            'formt' => $form->createView(), // The form for adding a new Type of Service
        ]);
    }
}
