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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChambreController extends AbstractController
{
    #[Route("/chambre", name: "app_chamber")]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        SluggerInterface $slugger
    ): Response {
        $chambre = new Chambre();
        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du numéro de chambre
            $numeroChB = $form->get('numeroChB')->getData();
            if (!preg_match('/^[A-Z]/', $numeroChB)) {
                $this->addFlash('error', 'Le numéro de chambre doit commencer par une lettre majuscule.');
                return $this->redirectToRoute('app_chamber');
            }
            $chambre->setNumeroChB($numeroChB);

            // Gestion de l'image
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Répertoire défini dans services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_chamber');
                }

                $chambre->setImage($newFilename);
            }

            $entityManager->persist($chambre);
            $entityManager->flush();

            $this->addFlash('success', 'Chambre ajoutée avec succès !');
            return $this->redirectToRoute('app_chamber');
        }

        $chambres = $chambreRepository->findAll();

        return $this->render('backtemplates/app_chambre.html.twig', [
            'form' => $form->createView(),
            'chambres' => $chambres,
        ]);
    }

    #[Route("/chambre/edit/{id}", name: "app_chambre_edit")]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ChambreRepository $chambreRepository,
        SluggerInterface $slugger
    ): Response {
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        $form = $this->createForm(ChambreType::class, $chambre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du numéro de chambre
            $numeroChB = $form->get('numeroChB')->getData();
            if (!preg_match('/^[A-Z]/', $numeroChB)) {
                $this->addFlash('error', 'Le numéro de chambre doit commencer par une lettre majuscule.');
                return $this->redirectToRoute('app_chambre_edit', ['id' => $id]);
            }
            $chambre->setNumeroChB($numeroChB);

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
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
                    return $this->redirectToRoute('app_chambre_edit', ['id' => $id]);
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
    public function delete(int $id, EntityManagerInterface $entityManager, ChambreRepository $chambreRepository): Response {
        $chambre = $chambreRepository->find($id);

        if (!$chambre) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

        $entityManager->remove($chambre);
        $entityManager->flush();

        $this->addFlash('success', 'Chambre supprimée avec succès !');
        return $this->redirectToRoute('app_chamber');
    }

    #[Route("/front/chambre", name: "app_front_chambre")]
    public function frontChambre(ChambreRepository $chambreRepository): Response {
        $chambres = $chambreRepository->findAll();

        if (!$chambres) {
            throw $this->createNotFoundException('Aucune chambre trouvée.');
        }

        return $this->render('fronttemplates/app_frontchambre.html.twig', [
            'chambres' => $chambres,
        ]);
    }
}
