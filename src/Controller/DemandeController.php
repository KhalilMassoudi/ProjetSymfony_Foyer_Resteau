<?php

namespace App\Controller;

use App\Form\DemandeFormType;
use App\Entity\DemandeService;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DemandeServiceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class DemandeController extends AbstractController
{
    #[Route('/service/dem', name: 'app_demande')]
    public function listDeamndes(DemandeServiceRepository $rep): Response
    {   $demandes=$rep->findByStatus('Under review');
        return $this->render('service/demande/Demandes_back.html.twig', [
            'demandes' => $demandes ,
        ]);
    }

    #[Route('/demande/ajout/{id}', name: 'app_demande_ajout')]
    public function ajouterDemande(
        int $id,
        ServiceRepository $serviceRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserInterface $user // Inject the authenticated user
    ): Response {
        // Récupération du service concerné
        $service = $serviceRepository->find($id);

        if (!$service) {
            throw $this->createNotFoundException('Le service demandé n\'existe pas.');
        }

        
        $demande = new DemandeService();
        $demande->setUser($user); 
        $demande->setService($service); 

        $form = $this->createForm(DemandeFormType::class, $demande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $demande->setDateDemande(new \DateTime());
            
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été envoyée avec succès.');

            
            return $this->redirectToRoute('app_frontend_services');
        }

        return $this->render('service/demande/Demande_front.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }
    #[Route('/reject-demande/{id}', name: 'app_demande_reject')]
    public function rejectDemande($id, DemandeServiceRepository $rep, ManagerRegistry $doc): Response
    {
        $demande = $rep->find($id);

        if (!$demande) {
            $this->addFlash('error', 'The demande does not exist.');
            return $this->redirectToRoute('app_demande_list');
        }

        $em = $doc->getManager();
        $demande->setStatus('Rejected'); 
        $em->flush();

        $this->addFlash('success', 'The demande has been rejected.');
        return $this->redirectToRoute('app_demande');
    }
    #[Route('/accept-demande/{id}', name: 'app_demande_accept')]
    public function acceptDemande($id, DemandeServiceRepository $rep, ManagerRegistry $doc): Response
    {
        $demande = $rep->find($id);

        if (!$demande) {
            $this->addFlash('error', 'The demande does not exist.');
            return $this->redirectToRoute('app_demande_list');
        }

        $em = $doc->getManager();
        $demande->setStatus('Accepted'); // Update the status
        $em->flush();

        $this->addFlash('success', 'The demande has been accepted.');
        return $this->redirectToRoute('app_demande');
    }
}
