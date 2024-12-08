<?php

namespace App\Controller;

use App\Form\DemandeFormType;
use App\Entity\DemandeService;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DemandeServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\FormType;


class DemandeController extends AbstractController
{
    #[Route('/demande', name: 'app_demande')]
    public function listDeamndes(DemandeServiceRepository $rep): Response
    {   $demandes=$rep->findAll();
        return $this->render('service/demande/Demandes_back.html.twig', [
            'demandes' => $demandes ,
        ]);
    }

    #[Route('/demande/ajout/{id}', name: 'app_demande_ajout')]
    public function ajouterDemande(
        int $id, 
        ServiceRepository $serviceRepository, 
        Request $request, 
        EntityManagerInterface $entityManager
    ): Response {
        // Récupération du service concerné
        $service = $serviceRepository->find($id);

        if (!$service) {
            throw $this->createNotFoundException('Le service demandé n\'existe pas.');
        }

        // Création de l'objet DemandeService
        $demande = new DemandeService();
        $form = $this->createForm(DemandeFormType::class, $demande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Association de la demande au service
            $demande->setDateDemande(new \DateTime());
            
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a été envoyée avec succès.');

            // Redirection vers une autre page (par exemple, la liste des services ou un récapitulatif)
            return $this->redirectToRoute('app_frontend_services');
        }

        return $this->render('service/demande/Demande_front.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }

}
