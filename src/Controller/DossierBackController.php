<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Dossier;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/dossier')]
class DossierBackController extends AbstractController
{
    #[Route('/', name: 'app_dossier_back_index', methods: ['GET'])]
    public function index(Request $request, DossierRepository $dossierRepository, PaginatorInterface $paginator): Response
    {
        // Fetch all dossiers from the database
        $allDossiers = $dossierRepository->findAll();

        // Paginate the dossiers using KnpPaginatorBundle
        $dossiers = $paginator->paginate(
            $allDossiers, // Data to paginate
            $request->query->getInt('page', 1), // Page number, default is 1
            5// Number of items per page
        );

        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }


    
    #[Route('/{id}/delete', name: 'app_dossier_back_delete', methods: ['GET'])]
    public function delete(Request $request, Dossier $dossier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($dossier);
        $entityManager->flush();

        return $this->redirectToRoute('app_dossier_back_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/', name:'dossiers_date')]
    public function dossiersByDate(DossierRepository $DossierRepository): Response
    {
        $dossiers = $DossierRepository->findByDate();
        
        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }


    
    #[Route('/cin', name:'dossiers_cin')]
     
    public function dossiersByEtat(DossierRepository $DossierRepository): Response
    {
        $dossiers = $DossierRepository->findBycin();
        
        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }
    
    #[Route('/montant', name:'dossiers_montant')]
    
    public function dossiersByMontant(DossierRepository $DossierRepository): Response
    {
        $dossiers = $DossierRepository->findByMontant();
        
        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }


}
