<?php

namespace App\Controller;

use App\Entity\DemandeDossier;
use App\Entity\Dossier;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use BaconQrCode\Writer;
use App\Services\QrcodeService;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\Result\PngResult;



#[Route('/dossier')]
class DossierFrontController extends AbstractController
{
    #[Route('/', name: 'app_dossier_index', methods: ['GET'])]
    public function index(DossierRepository $dossierRepository): Response
    {

        
        return $this->render('dossier/index.html.twig', [
            'dossiers' => $dossierRepository->findAll(),
        ]);
        
    }
    #[Route('/new', name: 'app_dossier_new', methods: ['GET', 'POST'])]
    public function new(SecurityController $security, Request $request, EntityManagerInterface $entityManager): Response
    {
        $dossier = new Dossier();
        $dossier->setDate(new \DateTime());
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the current user
            $user = $security->getUser();
            $dossier->setUser($user);
    
            // Persist and flush the dossier entity
            $entityManager->persist($dossier);
            $entityManager->flush();
    
            // Redirect to the dossier index page
            return $this->redirectToRoute('app_dossier_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Fetch demandes associated with the current user
        $user = $security->getUser();
        $demandeDossiers = $entityManager->getRepository(DemandeDossier::class)->findBy(['user' => $user]);
    
        return $this->renderForm('dossier/new.html.twig', [
            'dossier' => $dossier,
            'demandeDossiers' => $demandeDossiers, // Pass the demandes associated with the current user to the template
            'form' => $form,
        ]);
    }
    
   
   /* #[Route('/new', name: 'app_dossier_new', methods: ['GET', 'POST'])]
    public function new(SecurityController $security,Request $request, EntityManagerInterface $entityManager, QrcodeService $qrcodeService): Response
    {
        $dossier = new Dossier();
        $dossier->setDate(new \DateTime());
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);
        $qrCodeData = null;
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user=$security->getUser();
            $dossier->setUser($user);
            $entityManager->persist($dossier);
            $entityManager->flush();
          
            // Generate QR code
            $qrCodeData = $qrcodeService->generateQRCode($dossier->getId());
    
            return $this->redirectToRoute('app_dossier_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('dossier/new.html.twig', [
            'dossier' => $dossier,
            'form' => $form->createView(), // Pass the FormView object
            'qrCodeData' => $qrCodeData, // Pass qrCodeData to the template
        ]);
    }*/
    


    #[Route('/{id}', name: 'app_dossier_show', methods: ['GET'])]
    public function show(Dossier $dossier): Response
    {
        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dossier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dossier $dossier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dossier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dossier/edit.html.twig', [
            'dossier' => $dossier,
            'demandeDossiers' => $entityManager->getRepository(DemandeDossier::class)->findAll(),

            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_dossier_delete', methods: ['GET'])]
    public function delete(Request $request, Dossier $dossier, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($dossier);
        $entityManager->flush();

        return $this->redirectToRoute('app_dossier_index', [], Response::HTTP_SEE_OTHER);
    }
/*
    #[Route('/generate-qr-code/{id}', name: 'generate_qr_code')]
    public function generateQRCode($id, DossierRepository $dossierRepository): Response
    {
        // Fetch the dossier entity based on the provided ID
        $dossier = $dossierRepository->find($id);
    
        if (!$dossier) {
            // Handle case where dossier with the provided ID is not found
            throw $this->createNotFoundException('Dossier not found for ID ' . $id);
        }
    
        // Generate the QR code with the dossier ID
        $qrCode = new QrCode($dossier->getId());
        $qrCode->setSize(200);
    
        // Get the QR code image data
        $imageData = $qrCode->writeString();
    
        // Set the response headers
        $response = new Response($imageData, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'image/png');
    
        return $response;
    }
    */
    public function generateQrCode(Request $request): Response
    {
        // Generate QR code
        $qrCode = new QrCode('Your QR code data here');
        $writer = new PngWriter();
        $dataUri = $writer->write($qrCode)->getDataUri();
    
        // Return QR code image as response
        return new Response($dataUri);
    }
    
}
