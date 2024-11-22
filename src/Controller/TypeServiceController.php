<?php

namespace App\Controller;

use App\Entity\TypeService;
use App\Form\TypeServiceFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
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
            $this->addFlash('success', 'New TypeService has been successfully added.');
            var_dump($type->getId()); 
            
            return $this->redirectToRoute('app_type_service'); // Redirect after adding service
        }
    
        return $this->render('service/TypeService.html.twig', [
            'type' => $types, // List of types
            'formt' => $form->createView(), // The form for adding a new Type of Service
        ]);
    }
    #[Route('/suppType/{id}', name: 'app_TypeServiceSupprim')]
    public function SuppTypeService(int $id, TypeServiceRepository $rep, ManagerRegistry $doc): Response
    {
        // Retrieve the service to delete
        $type = $rep->find($id);
        
        // Check if the entity exists
        if (!$type) {
            // If not found, redirect with an error message
            $this->addFlash('error', 'The TypeService you are trying to delete does not exist.');
            return $this->redirectToRoute('app_type_service');
        }
    
        // Get the entity manager
        $em = $doc->getManager();
    
        // Remove the entity
        $em->remove($type);
        $em->flush(); // Commit to the database
    
        // Add a success message
        $this->addFlash('success', 'The TypeService has been successfully deleted.');
    
        // Redirect back to the type service page
        return $this->redirectToRoute('app_type_service');
    }
    
    
}
