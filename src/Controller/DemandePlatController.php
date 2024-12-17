<?php

namespace App\Controller;

use App\Form\DemandePlatFormType;
use App\Entity\DemandePlat;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DemandePlatController extends AbstractController
{
    #[Route('/demande/plat', name: 'app_demande_plat')]
    public function listDemandes(EntityManagerInterface $em): Response
    {
        $demandes = $em->getRepository(DemandePlat::class)->findBy(['status' => 'Under review']);
        
        return $this->render('backtemplates/Demandes_back.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    
     #[Route('front/demande/plat/ajout/{id}', name: 'app_demande_plat_ajout')]
public function ajouterDemande(
    int $id,
    PlatRepository $platRepository,
    Request $request,
    EntityManagerInterface $entityManager,
    UserInterface $user // Inject the authenticated user
): Response {
    // Récupération du plat concerné
    $plat = $platRepository->find($id);

    if (!$plat) {
        throw $this->createNotFoundException('Le plat demandé n\'existe pas.');
    }

    // Vérification si la quantité du plat est à zéro
    if ($plat->getQuantite() <= 0) {
        $this->addFlash('error', 'Ce plat est en rupture de stock. Vous ne pouvez pas faire une demande pour ce plat.');
        return $this->redirectToRoute('app_plats_list');  // Rediriger vers la liste des plats
    }

    // Création de la demande de plat
    $demandePlat = new DemandePlat();
    $demandePlat->setUser($user);
    $demandePlat->setPlat($plat);

    // Création du formulaire
    $form = $this->createForm(DemandePlatFormType::class, $demandePlat);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Définir la date de la demande
        $demandePlat->setDateDemande(new \DateTime());

        // Sauvegarde de la demande dans la base de données
        $entityManager->persist($demandePlat);
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash('success', 'Votre demande de plat a été envoyée avec succès.');

        // Rediriger vers la page des plats ou une autre page
        return $this->redirectToRoute('app_plats_list');
    }

    return $this->render('fronttemplates/DemandePlat_front.html.twig', [
        'form' => $form->createView(),
        'plat' => $plat,
    ]);
}


    #[Route('/reject-demande-plat/{id}', name: 'app_demande_plat_reject')]
    public function rejectDemande(int $id, EntityManagerInterface $em): Response
    {
        $demandePlat = $em->getRepository(DemandePlat::class)->find($id);

        if (!$demandePlat) {
            $this->addFlash('error', 'La demande de plat n\'existe pas.');
            return $this->redirectToRoute('app_demande_plat');
        }

        $demandePlat->setStatus('Rejected');
        $em->flush();

        $this->addFlash('success', 'La demande de plat a été rejetée.');
        return $this->redirectToRoute('app_demande_plat');
    }

    #[Route('/accept-demande-plat/{id}', name: 'app_demande_plat_accept')]
    public function acceptDemande(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer la demande de plat
        $demandePlat = $em->getRepository(DemandePlat::class)->find($id);
        
        if (!$demandePlat) {
            $this->addFlash('error', 'La demande de plat n\'existe pas.');
            return $this->redirectToRoute('app_demande_plat');
        }
        
        // Récupérer le plat associé
        $plat = $demandePlat->getPlat();
        if (!$plat) {
            $this->addFlash('error', 'Aucun plat associé à cette demande.');
            return $this->redirectToRoute('app_demande_plat');
        }
        
        // Vérifier si le plat a une quantité suffisante
        if ($plat->getQuantite() <= 0) {
            $this->addFlash('error', 'Le plat est en rupture de stock. La demande ne peut pas être acceptée.');
            return $this->redirectToRoute('app_demande_plat');
        }
        
        // Décrémenter la quantité du plat
        $plat->setQuantite($plat->getQuantite() - 1);
        
        // Si la quantité devient 0, l'étudiant ne peut plus faire une demande
        if ($plat->getQuantite() == 0) {
            $this->addFlash('warning', 'Le plat est désormais en rupture de stock. Plus aucune demande ne peut être effectuée pour ce plat.');
        }
        
        // Mettre à jour le statut de la demande
        $demandePlat->setStatus('Accepted');
        
        // Sauvegarder les modifications
        $em->persist($plat);
        $em->persist($demandePlat);
        $em->flush();
        
        $this->addFlash('success', 'La demande de plat a été acceptée et la quantité du plat mise à jour.');
        return $this->redirectToRoute('app_demande_plat');
    }
    
}
