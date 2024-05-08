<?php

namespace App\Controller;

use App\Entity\DemandeDossier;
use App\Form\DemandeDossierType;
use App\Repository\DemandeDossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/demande')]
class DemandeDossierBackController extends AbstractController
{
    #[Route('/', name: 'app_demande_back_dossier_index', methods: ['GET'])]
    public function index(DemandeDossierRepository $demandeDossierRepository): Response
    {
        return $this->render('demande_dossier/indexB.html.twig', [
            'demandeDossiers' => $demandeDossierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_demande_back_dossier_new', methods: ['GET', 'POST'])]
    public function new(SecurityController $security,Request $request, EntityManagerInterface $entityManager): Response
    {
        $demandeDossier = new DemandeDossier();
        $form = $this->createForm(DemandeDossierType::class, $demandeDossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user=$security->getUser();
            $demandeDossier->setUser($user);
            $entityManager->persist($demandeDossier);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_back_dossier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_dossier/new.html.twig', [
            'demande_dossier' => $demandeDossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_back_dossier_show', methods: ['GET'])]
    public function show(DemandeDossier $demandeDossier): Response
    {
        return $this->render('demande_dossier/show.html.twig', [
            'demande_dossier' => $demandeDossier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_back_dossier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeDossier $demandeDossier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeDossierType::class, $demandeDossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_back_dossier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_dossier/edit.html.twig', [
            'demande_dossier' => $demandeDossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_demande_back_dossier_delete', methods: ['get'])]
    public function delete(Request $request, DemandeDossier $demandeDossier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($demandeDossier);
        $entityManager->flush();

        return $this->redirectToRoute('app_demande_back_dossier_index', [], Response::HTTP_SEE_OTHER);
    }
}
