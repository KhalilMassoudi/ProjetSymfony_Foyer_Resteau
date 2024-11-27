<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use App\Enum\ChambreStatut;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChambreController extends AbstractController
{
    #[Route("/chambre", name: "app_chamber")]
    public function index(Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository, SluggerInterface $slugger): Response
    {

        $chambre = new Chambre();


        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier et assigner le statut de chambre à partir de l'énumération
            $statut = $form->get('statutChB')->getData();
            $chambre->setStatutChB($statut);


            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer l'image dans le répertoire de destination
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Spécifier le répertoire d'upload
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs d'upload
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_chamber');
                }

                // Assigner le nom de fichier à l'entité Chambre
                $chambre->setImage($newFilename);
            }


            $prix = $form->get('prixChB')->getData();
            $chambre->setPrixChB($prix);


            $entityManager->persist($chambre);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('success', 'Chambre ajoutée avec succès !');


            return $this->redirectToRoute('app_chamber');
        }


        $chambres = $chambreRepository->findAll();

        // Rendre la vue avec le formulaire et la liste des chambres
        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
        ]);
    }

    #[Route("/chambre/edit/{id}", name: "app_chambre_edit")]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository, SluggerInterface $slugger): Response
    {
        // Récupérer la chambre par ID
        $chambre = $chambreRepository->find($id);

        // Vérifier si la chambre existe
        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }


        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier et assigner le statut de chambre à partir de l'énumération
            $statut = $form->get('statutChB')->getData();
            $chambre->setStatutChB($statut);


            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Générer un nom unique pour l'image
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();


                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_chamber');
                }


                $chambre->setImage($newFilename);
            }


            $entityManager->flush();


            $this->addFlash('success', 'Chambre modifiée avec succès !');


            return $this->redirectToRoute('app_chamber');
        }


        return $this->render('backtemplates/app_edit_chambre.html.twig', [
            'form' => $form->createView(),
            'chambre' => $chambre,
        ]);
    }

    #[Route("/chambre/delete/{id}", name: "app_chambre_delete")]
    public function delete(int $id, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response
    {
        // Récupérer la chambre par ID
        $chambre = $chambreRepository->find($id);

        // Vérifier si la chambre existe
        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }


        $entityManager->remove($chambre);
        $entityManager->flush();


        $this->addFlash('success', 'Chambre supprimée avec succès !');


        return $this->redirectToRoute('app_chamber');
    }
    #[Route("/front/chambre", name: "app_front_chambre")]
    public function frontChambre(ChambreRepository $chambreRepository): Response
    {
        $chambres = $chambreRepository->findAll();

        return $this->render('fronttemplates/app_frontchambre.html.twig', [
            'chambres' => $chambres,
        ]);
    }

}
