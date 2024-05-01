<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('/', name: 'app_voiture_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $entityManager->getRepository(Voiture::class)->createQueryBuilder('v')->getQuery();
    
        $voitures = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // numéro de la page, par défaut 1
            4 // nombre d'éléments par page
        );
    
        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/new', name: 'app_voiture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le téléchargement de l'image
            $imageFile = $form->get('img')->getData();
    
            // Vérifier si un fichier a été téléchargé
            if ($imageFile) {
                // Générer un nom de fichier unique
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
                // Déplacer le fichier téléchargé vers le répertoire où vous souhaitez le stocker
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'), // Paramètre configuré dans config/services.yaml
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs éventuelles lors du téléchargement du fichier
                }
    
                // Mettre à jour l'entité Voiture avec le chemin de l'image
                $voiture->setImg($newFilename);
            }
    
            // Persistez et enregistrez l'entité
            $entityManager->persist($voiture);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('voiture/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    #[Route('/{idv}', name: 'app_voiture_show', methods: ['GET'])]
    public function show(Voiture $voiture): Response
    {
        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    #[Route('/{idv}/edit', name: 'app_voiture_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(VoitureType::class, $voiture);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload if a new image file has been provided
        $imageFile = $form->get('img')->getData();
        if ($imageFile) {
            // Generate a unique filename
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            // Move the uploaded file to the desired directory
            try {
                $imageFile->move(
                    $this->getParameter('img_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle file upload error
            }
            // Update the entity with the new image filename
            $voiture->setImg($newFilename);
        }

        // Persist and save the entity
        $entityManager->flush();

        return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('voiture/edit.html.twig', [
        'voiture' => $voiture,
        'form' => $form,
    ]);
}

    #[Route('/{idv}', name: 'app_voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getIdv(), $request->request->get('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
