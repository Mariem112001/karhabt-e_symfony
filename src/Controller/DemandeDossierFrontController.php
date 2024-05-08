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



#[Route('/demande')]
class DemandeDossierFrontController extends AbstractController
{
            #[Route('/', name: 'app_demande_dossier_index', methods: ['GET'])]
        public function index(SecurityController $security, DemandeDossierRepository $demandeDossierRepository): Response
        {
            // Get the current logged-in user
            $user = $security->getUser();

            // Fetch reservations for the current user
            $demandeyQuery = $demandeDossierRepository->findBy(['user' => $user]);

            if (!$demandeyQuery) {
                return $this->redirectToRoute('app_demande_dossier_new');
            }

            // Since you're expecting only one demande_dossier object,
            // fetch the first one from the result array
            $demandeDossier = $demandeyQuery[0];

            return $this->render('demande_dossier/showF.html.twig', [
                'demande_dossier' => $demandeDossier,
            ]);
        }


    #[Route('/new', name: 'app_demande_dossier_new', methods: ['GET', 'POST'])]
    public function new(SecurityController $security,Request $request, EntityManagerInterface $entityManager): Response
    {
        $demandeDossier = new DemandeDossier();
        $form = $this->createForm(DemandeDossierType::class, $demandeDossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // move files  
            $user=$security->getUser();
            $demandeDossier->setUser($user);
            
            $cin = $form->get('urlcin')->getData();
            $cerretenu = $form->get('urlcerretenu')->getData();
            $atttravail = $form->get('urlatttravail')->getData();
            $decrevenu = $form->get('urldecrevenu')->getData();
            $extnaissance = $form->get('urlextnaissance')->getData();

            var_dump($cin);

            $cin->move('uploads', $cin->getClientOriginalName());
            $cerretenu->move('uploads', $cerretenu->getClientOriginalName());
            $atttravail->move('uploads', $atttravail->getClientOriginalName());
            $decrevenu->move('uploads', $decrevenu->getClientOriginalName());
            $extnaissance->move('uploads', $extnaissance->getClientOriginalName());

            $demandeDossier->setUrlcin('/uploads/' . $cin->getClientOriginalName());
            $demandeDossier->setUrlcerretenu('/uploads/' . $cerretenu->getClientOriginalName());
            $demandeDossier->setUrlatttravail('/uploads/' . $atttravail->getClientOriginalName());
            $demandeDossier->setUrldecrevenu('/uploads/' . $decrevenu->getClientOriginalName());
            $demandeDossier->setUrlextnaissance('/uploads/' . $extnaissance->getClientOriginalName());

            $entityManager->persist($demandeDossier);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_dossier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_dossier/new.html.twig', [
            'demande' => $demandeDossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_dossier_show', methods: ['GET'])]
    public function show(DemandeDossier $demandeDossier): Response
    {
        return $this->render('demande_dossier/show.html.twig', [
            'demande_dossier' => $demandeDossier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_dossier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DemandeDossier $demandeDossier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeDossierType::class, $demandeDossier);
        $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
        // $entityManager->flush();

        $cin = $form->get('urlcin')->getData();
        $cerretenu = $form->get('urlcerretenu')->getData();
        $atttravail = $form->get('urlatttravail')->getData();
        $decrevenu = $form->get('urldecrevenu')->getData();
        $extnaissance = $form->get('urlextnaissance')->getData();

        if ($cin) {
            $cin->move('uploads', $cin->getClientOriginalName());
            $demandeDossier->setUrlcin('/uploads/' . $cin->getClientOriginalName());
        }

        if ($cerretenu) {
            $cerretenu->move('uploads', $cerretenu->getClientOriginalName());
            $demandeDossier->setUrlcerretenu('/uploads/' . $cerretenu->getClientOriginalName());
        }

        if ($atttravail) {
            $atttravail->move('uploads', $atttravail->getClientOriginalName());
            $demandeDossier->setUrlatttravail('/uploads/' . $atttravail->getClientOriginalName());
        }

        if ($decrevenu) {
            $decrevenu->move('uploads', $decrevenu->getClientOriginalName());
            $demandeDossier->setUrldecrevenu('/uploads/' . $decrevenu->getClientOriginalName());
        }

        if ($extnaissance) {
            $extnaissance->move('uploads', $extnaissance->getClientOriginalName());
            $demandeDossier->setUrlextnaissance('/uploads/' . $extnaissance->getClientOriginalName());
        }

        $entityManager->flush();

            return $this->redirectToRoute('app_demande_dossier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('demande_dossier/edit.html.twig', [
            'demande_dossier' => $demandeDossier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_demande_dossier_delete', methods: ['GET'])]
    public function delete(Request $request, DemandeDossier $demandeDossier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($demandeDossier);
        $entityManager->flush();

        return $this->redirectToRoute('app_demande_dossier_index', [], Response::HTTP_SEE_OTHER);
    }
}
