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
        $produitsEnRupture = $dossierRepository->findProduitsEnRupture();

        $nom = $request->query->get('Nom');
       // $tri = $request->query->get('tri', 'nom');
        /*if ($nom) {
            $allDossiers = $dossierRepository->searchAndSort($nom, $tri);
        } else {
            $allDossiers = $dossierRepository->findAllSorted($tri);
        }*/
        if (!empty($produitsEnRupture)) {
            $message = '';
            foreach ($produitsEnRupture as $produitEnRupture) {
                $message .= sprintf('%s a payé plus que 5$ ', $produitEnRupture->getNom());
            }
            $this->addFlash('warning', $message);
        }
    

        // Paginate the dossiers using KnpPaginatorBundle
        $dossiers = $paginator->paginate(
            $allDossiers, // Data to paginate
            $request->query->getInt('page', 1), // Page number, default is 1
            5// Number of items per page
        );

        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
            'produitsEnRupture' => $produitsEnRupture,
        ]);
    }
    private function convertQrCodeResultToString(PngResult $qrCodeResult): string
{
    // Convert the result to a string (e.g., base64 encode the image)
    // Adjust this logic based on how you want to represent the QR code data
    return 'data:image/png;base64,' . base64_encode($qrCodeResult->getString());
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


  
    
    #[Route('/', name:'dossiers_montant')]
    
    public function dossiersByMontant(DossierRepository $DossierRepository): Response
    {
        $dossiers = $DossierRepository->findByMontant();
        
        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }
    #[Route('/cin', name:'dossiers_cin')]
    
    public function dossiersByCin(DossierRepository $DossierRepository): Response
    {
        $dossiers = $DossierRepository->findByCin();
        
        return $this->render('dossier/indexBack.html.twig', [
            'dossiers' => $dossiers,
        ]);
    }

  

    #[Route('/search', name: 'dossier_search')] // Define the route and provide a unique name
    public function searchByCin(DossierRepository $DossierRepository, Request $request): Response
    {
        $cin = $request->query->get('cin');
    
        // Call the repository method to search for dossier by CIN
        $dossier = $dossierRepository->searchByCin($cin);
    
        return $this->render('dossier/indexBack.html.twig', [
            'dossier' => $dossier,
        ]);
    }
    

}