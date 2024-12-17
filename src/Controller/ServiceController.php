<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\DeamndeServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class ServiceController extends AbstractController
{
     
    #[Route('/service', name: 'app_service')]
    public function AfficherAllServices(
        DeamndeServiceRepository $demandeRepository,
        ServiceRepository $serviceRepository, 
        Request $request, 
        EntityManagerInterface $em
    ): Response
    {
        // Retrieve the list of users by service
        $usersByService = $demandeRepository->getUsersByService();

        // Search logic
        $searchTerm = $request->query->get('search', '');
        $services = $searchTerm 
            ? $serviceRepository->findServiceByNameOrType($searchTerm)
            : $serviceRepository->findAll();

        // Fetch statistics from the repository
        $totalServices = count($services);
        $totalUsers = $serviceRepository->getTotalUsers();
        $usersInscrits = $serviceRepository->getUsersInscrits();
        $usersNonInscrits = $totalUsers - $usersInscrits;
        $serviceStats = $serviceRepository->getServiceStatistics();

        // Form handling for adding a new service
        $service = new Service();
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();
            $this->addFlash('success', 'New Service has been successfully added.');
            return $this->redirectToRoute('app_service');
        }

        // Render the service management page with the added statistics
        return $this->render('service/GestionServices.html.twig', [
            'services' => $services,
            'form' => $form->createView(),
            'searchTerm' => $searchTerm,
            'usersByService' => $usersByService,
            'totalServices' => $totalServices,
            'totalUsers' => $totalUsers,
            'usersInscrits' => $usersInscrits,
            'usersNonInscrits' => $usersNonInscrits,
            'serviceStats' => $serviceStats,
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
        $this->addFlash('success', 'The TypeService has been successfully deleted.');
        return $this->redirectToRoute('app_service');
    }
    #[Route('/edit/{id}', name: 'app_service_edit')]
    public function modifyS($id,ServiceRepository $rep , ManagerRegistry $doc,Request $request): Response
    {
        $service=$rep->find(($id));
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);
        if($form->isSubmitted()  && $form->isValid()){
            $em=$doc->getManager();
            $em->flush();
            $this->addFlash('success', 'The service has been successfully updated.');
            return $this->redirectToRoute('app_service');
        }
        return $this->render('service/edit_Service.html.twig',[
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }
    #[Route('/front/nos-services', name: 'app_frontend_services')]
    public function afficherServicesFrontend(Request $request, ServiceRepository $serviceRepository): Response
    {
        $searchTerm = $request->query->get('search', '');
    
        $services = $searchTerm 
        ? $serviceRepository->findServiceByNameOrType($searchTerm) 
        : $serviceRepository->findAll();
    
        return $this->render('service/Nos_Service.html.twig', [
            'services' => $services,
            'searchTerm' => $searchTerm,
        ]);
    }
    


}