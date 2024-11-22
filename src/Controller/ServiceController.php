<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\TypeService;
use App\Form\ServiceFormType;
use App\Form\TypeServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
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
    
    
    $service = new Service();
    $form = $this->createForm(ServiceFormType::class, $service);
    $form->handleRequest($request);

    
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($service);
        $em->flush();
        
        return $this->redirectToRoute('app_service'); 
    }

    return $this->render('service/GestionServices.html.twig', [
        'service' => $services, // List of services
        'form' => $form->createView(), // The form for adding a new service
    ]);
}
    #[Route('/supp/{id}', name: 'app_ServiceSupprim')]
    public function SuppS($id,ServiceRepository $rep,ManagerRegistry $doc): Response
    {   //Recuperer le service a supprimer
        $service=$rep->find($id);
        //supprimer les service
        $em=$doc->getManager();
        $em->remove($service);
        $em->flush();//Commit au niveau du base de données
        return $this->redirectToRoute('app_service');
    }
    
}

