<?php

namespace App\Controller;
use App\Entity\Dossier;
use App\Entity\DemandeDossier;
use App\Repository\DossierRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PdfController',
        ]);
    }


    #[Route("/pdf/dossierb/{id}", name: "pdf_dossier")]
    public function pdf_dossier($id)
    {
        // Récupérer la liste des utilisateurs depuis la base de données
        $dossiers = $this->getDoctrine()->getRepository(Dossier::class)->findByid($id);
        $demandeDossiers = $this->getDoctrine()->getRepository(DemandeDossier::class)->findByid($id);

        // Générer le contenu du PDF avec la liste des utilisateurs
        $html = $this->renderView('pdf/dossier.html.twig', [
            'dossiers' => $dossiers,
            'demandeDossiers' => $demandeDossiers,
        ]);

   


  // Récupérer l'heure actuelle
        

        // Créer une nouvelle instance de TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Définir les propriétés du document PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Karhatb-e');
        $pdf->SetTitle('KARHABT-E * Bon d achat   ');
        $pdf->SetSubject('Liste des bons d achat');
        $pdf->SetKeywords('Liste, bon d achat');



        // Ajouter une page au document PDF
        $pdf->AddPage();



        // Écrire le contenu HTML dans le document PDF
        $pdf->writeHTML($html, true, false, true, false, '');



        // Ajouter l'heure en bas de la dernière page
        $pdf->SetY(260);
        $pdf->SetFont('helvetica', 'I', 12);
        $now = new \DateTime();
        $formattedDateTime = $now->format('d/m/Y H:i:s');
        $pdf->SetXY(10, 10); // Adjust the position
        $pdf->Cell(0, 10, 'Généré le: ' . $formattedDateTime, 0, 1, 'L');
        
        // Générer le fichier PDF et l'envoyer au navigateur
        return new Response($pdf->Output('Liste des bons d achat.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Liste des bons d achat.pdf"',


           
            // JavaScript function to display the current date and time
         
        
    
            // Call the function when the page loads
          
        
        ]);
    }
   
}
