<?php

namespace App\Controller;

use App\Entity\Arrivage;
use App\Entity\Voiture;
use App\Form\ArrivageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/arrivage')]
class ArrivageController extends AbstractController
{
    #[Route('/', name: 'app_arrivage_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $arrivages = $entityManager
            ->getRepository(Arrivage::class)
            ->findAll();

        return $this->render('arrivage/index.html.twig', [
            'arrivages' => $arrivages,
        ]);
    }

    #[Route('/new', name: 'app_arrivage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $arrivage = new Arrivage();
        $form = $this->createForm(ArrivageType::class, $arrivage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($arrivage);
            $entityManager->flush();

            return $this->redirectToRoute('app_arrivage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('arrivage/new.html.twig', [
            'arrivage' => $arrivage,
            'form' => $form,
        ]);
    }

    #[Route('/{ida}', name: 'app_arrivage_show', methods: ['GET'])]
    public function show(Arrivage $arrivage): Response
    {
        return $this->render('arrivage/show.html.twig', [
            'arrivage' => $arrivage,
        ]);
    }

    #[Route('/{ida}/edit', name: 'app_arrivage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Arrivage $arrivage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArrivageType::class, $arrivage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_arrivage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('arrivage/edit.html.twig', [
            'arrivage' => $arrivage,
            'form' => $form,
        ]);
    }

    #[Route('/{ida}', name: 'app_arrivage_delete', methods: ['POST'])]
    public function delete(Request $request, Arrivage $arrivage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$arrivage->getIda(), $request->request->get('_token'))) {
            $entityManager->remove($arrivage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_arrivage_index', [], Response::HTTP_SEE_OTHER);
    }
}
