<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Form\DemandeFormType;
use App\Entity\DemandeService;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use App\Repository\ServiceRepository;
use Symfony\Component\Mailer\Transport;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\DeamndeServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class DemandeController extends AbstractController
{   private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/service/dem', name: 'app_demande')]
    public function listAndFilterDemandes(
        Request $request, 
        DeamndeServiceRepository $demandeRepository,
        ServiceRepository $serviceRepository
    ): Response {
        $services = $serviceRepository->findAll();

        $criteria = [
            'status' => $request->query->get('status', 'Under review'), 
            'service' => $request->query->get('service'),
            'date_min' => $request->query->get('date_min'),
            'date_max' => $request->query->get('date_max'),
            'user' => $request->query->get('user'),
        ];

        $demandes = $demandeRepository->searchAndFilter($criteria);

        return $this->render('service/demande/Demandes_back.html.twig', [
            'demandes' => $demandes,
            'services' => $services,
        ]);
    }


    #[Route('/demande/ajout/{id}', name: 'app_demande_ajout')]
    public function ajouterDemande(
        int $id,
        ServiceRepository $serviceRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Security $security
    ): Response {
        // Fetch the currently authenticated user
        $user = $security->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $service = $serviceRepository->find($id);

        if (!$service) {
            throw $this->createNotFoundException('Le service demandé n\'existe pas.');
        }

        // Create the Demande
        $demande = new DemandeService();
        $demande->setUser($user);
        $demande->setService($service);

        $form = $this->createForm(DemandeFormType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setDateDemande(new \DateTime());
            $entityManager->persist($demande);
            $entityManager->flush();

            $email = (new Email())
                ->from('azizchehata47@gmail.com')
                ->to($demande->getEmail())
                ->subject('mail de confirmation')
                ->text('Votre demande a bien été soumise. Nous vous contacterons bientôt.');

            $mailer->send($email);

            return $this->redirectToRoute('app_frontend_services');
        }

        return $this->render('service/demande/Demande_front.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }


    #[Route('/reject-demande/{id}', name: 'app_demande_reject')]
    public function rejectDemande($id, DeamndeServiceRepository $rep, ManagerRegistry $doc): Response
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
    public function acceptDemande($id, DeamndeServiceRepository $rep, ManagerRegistry $doc): Response
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

    #[Route('/demande/pdf', name: 'app_demande_pdf')]
    public function generatePdf(DeamndeServiceRepository $demandeRepository): Response
    {
        // Fetch demandes from the repository
        $demandes = $demandeRepository->findAll();

        // Calculate total price
        $totalPrix = array_sum(array_map(function ($demande) {
            return $demande->getService()->getPrix(); // Ensure `getService()->getPrix()` is correct
        }, $demandes));

        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        // Generate HTML for the PDF
        $html = $this->renderView('service/demande/pdf.html.twig', [
            'demandes' => $demandes,
            'totalPrix' => $totalPrix,
        ]);

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the PDF
        $dompdf->render();

        // Output the generated PDF to the browser
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="demandes.pdf"',
        ]);
    }

}
