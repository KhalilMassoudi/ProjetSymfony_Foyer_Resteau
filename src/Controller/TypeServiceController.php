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
use Symfony\Component\Validator\Constraints as Assert ;

class TypeServiceController extends AbstractController
{
    #[Route('/service/type', name: 'app_type_service')]
    public function AfficherAllTypes(TypeServiceRepository $rep, Request $request, EntityManagerInterface $em): Response
    {
        $types = $rep->findAll();
        

        $type = new TypeService();
        $form = $this->createForm(TypeServiceFormType::class, $type);
        $form->handleRequest($request);
    

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
    public function deleteTypeService(
        $id,
        TypeServiceRepository $typeServiceRepository,
        EntityManagerInterface $em
    ): Response {
        $typeService = $typeServiceRepository->find($id);
        if (!$typeService) {
            $this->addFlash('error', 'TypeService not found.');
            return $this->redirectToRoute('app_type_service');
        }
        foreach ($typeService->getServices() as $service) {
            $em->remove($service);
        }
        $em->remove($typeService);
        $em->flush();
        $this->addFlash('success', 'TypeService and related services successfully deleted.');
        return $this->redirectToRoute('app_type_service');
    }

    #[Route('/service/type/edit/{id}', name: 'app_type_service_edit')]
    public function edittype($id,TypeServiceRepository $rep, Request $request, EntityManagerInterface $em): Response
    {
        $type = $rep->find($id);
        $form = $this->createForm(TypeServiceFormType::class, $type);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() ) {
            $em->flush();
            $this->addFlash('success', 'New TypeService has been successfully updated.');
            var_dump($type->getId()); 
            return $this->redirectToRoute('app_type_service');
        }
    
        return $this->render('service/edit_TypeService.html.twig', [
            'type' => $type,
            'formt' => $form->createView(),
        ]);
    }
    
}
