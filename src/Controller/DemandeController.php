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
        $data = $demandeRepository->getDemandsByUser();
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
            'data' => $data,
        ]);
    }


    #[Route('/ajout/demande/{id}', name: 'app_demande_ajout')]
    public function ajouterDemande(
        int $id,
        ServiceRepository $serviceRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        Security $security
    ): Response {
        
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
    public function rejectDemande($id, DeamndeServiceRepository $rep, ManagerRegistry $doc , MailerInterface $mailer): Response
    {
        $demande = $rep->find($id);

        if (!$demande) {
            $this->addFlash('error', 'The demande does not exist.');
            return $this->redirectToRoute('app_demande_list');
        }

        $em = $doc->getManager();
        $user=$demande->getUser();
        $demande->setStatus('Rejected'); 
        $em->flush();
        // Créer le contenu HTML de l'email
        $htmlContent = "
        <html>
        <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
            }
            .email-container {
                margin: 20px auto;
                padding: 20px;
                max-width: 600px;
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 10px;
            }
            .email-header {
                background-color: #FF0000;
                color: white;
                padding: 10px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .email-content {
                padding: 20px;
                text-align: left;
            }
            .email-footer {
                margin-top: 20px;
                font-size: 0.9em;
                color: #666;
                text-align: center;
            }
        </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h2>Demande Rejetée</h2>
                </div>
                <div class='email-content'>
                    <p>Bonjour <strong>{$user->getUsername()}</strong>,</p>
                    <p>Nous vous informons que votre demande concernant le service a été <strong>rejetée</strong>.</p>
                    <p>Si vous avez des questions ou souhaitez discuter de cette décision, n'hésitez pas à nous contacter.</p>
                    <p>Merci de votre compréhension.</p>
                </div>
                <div class='email-footer'>
                    <p>Service des demandes - Votre Entreprise</p>
                    <p>Contact : support@votreentreprise.com</p>
                </div>
            </div>
        </body>
        </html>
        ";

        // Créer et envoyer l'email
        $email = (new Email())
        ->from('azizchehata47@gmail.com')
        ->to($user->getEmail())
        ->subject('Statut de votre demande')
        ->html($htmlContent);

        $mailer->send($email);
        $this->addFlash('success', 'The demande has been rejected.');
        return $this->redirectToRoute('app_demande');
    }
    #[Route('/accept-demande/{id}', name: 'app_demande_accept')]
    public function acceptDemande($id, DeamndeServiceRepository $rep, ManagerRegistry $doc , MailerInterface $mailer): Response
    {
        $demande = $rep->find($id);

        if (!$demande) {
            $this->addFlash('error', 'The demande does not exist.');
            return $this->redirectToRoute('app_demande_list');
        }

        $em = $doc->getManager();
        $user=$demande->getUser();
        $demande->setStatus('Accepted'); // Update the status
        $em->flush();

        $htmlContent = "
<html>
<head>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }
    .email-container {
        margin: 20px auto;
        padding: 20px;
        max-width: 600px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
    }
    .email-header {
        background-color: #4CAF50; /* Green for acceptance */
        color: white;
        padding: 10px;
        text-align: center;
        border-radius: 10px 10px 0 0;
    }
    .email-content {
        padding: 20px;
        text-align: left;
    }
    .email-footer {
        margin-top: 20px;
        font-size: 0.9em;
        color: #666;
        text-align: center;
    }
</style>
</head>
<body>
    <div class='email-container'>
        <div class='email-header'>
            <h2>Demande Acceptée</h2>
        </div>
        <div class='email-content'>
            <p>Bonjour <strong>{$user->getUsername()}</strong>,</p>
            <p>Nous sommes ravis de vous informer que votre demande concernant le service a été <strong>acceptée</strong>.</p>
            <p>Nous vous remercions pour votre intérêt et votre confiance. Notre équipe se tient à votre disposition pour toute information supplémentaire.</p>
            <p>Merci et à très bientôt.</p>
        </div>
        <div class='email-footer'>
            <p>Service des demandes - Votre Entreprise</p>
            <p>Contact : support@votreentreprise.com</p>
        </div>
    </div>
</body>
</html>
";      
         // Créer et envoyer l'email
         $email = (new Email())
         ->from('azizchehata47@gmail.com')
         ->to($user->getEmail())
         ->subject('Statut de votre demande')
         ->html($htmlContent);
 
         $mailer->send($email);

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