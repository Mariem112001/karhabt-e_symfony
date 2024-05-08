<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\Commentaire;
use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentaireFormType;
use App\Form\CommentaireType;
use App\Form\RatingFormType;
use App\Form\ReponseFormType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\Result\PngResult;
use Symfony\Component\HttpFoundation\Request;


class home_blogController extends AbstractController
{
    
    #[Route('/', name: 'app_home')]
    public function index(ActualiteRepository $actualiteRepository): Response
{
    $actualites = $actualiteRepository->findAll();
    foreach ($actualites as $actualite) {
        $date = $actualite->getDate();
        if ($date !== null) {
            $formattedDate = $date->format('Y-m-d');
            $actualite->setQrCode($formattedDate); // You can customize the QR code data as needed
        }
    }
    foreach ($actualites as $actualite) {
        // Check if $this->qrCodeBuilder is not null
        if ($this->qrCodeBuilder !== null) {
            // Get the date from the event
            $date = $actualite->getDate();

            // Check if the date is not null
            if ($date !== null) {
                // Convert the date to a string representation
                $formattedDate = $date->format('Y-m-d');

                // Customize the QR code data
                $qrCodeResult = $this->qrCodeBuilder
                    ->data($formattedDate)
                    ->build();

                // Convert the QR code result to a string representation
                $qrCodeString = $this->convertQrCodeResultToString($qrCodeResult);

                // Add the QR code string to the event entity
                $actualite->setQrCode($qrCodeString);
            }
        }
    }

    return $this->render('home_blog/index.html.twig', [
        'actualites' => $actualites,
    ]);
}
   
private function convertQrCodeResultToString(PngResult $qrCodeResult): string
{
    // Convert the result to a string (e.g., base64 encode the image)
    // Adjust this logic based on how you want to represent the QR code data
    return 'data:image/png;base64,' . base64_encode($qrCodeResult->getString());
}

#[Route('/{id}', name: 'app_actualite_show_front', methods: ['GET', 'POST'])]
public function show(Request $request, Actualite $actualite): Response
{
    // Create the rating form
    $ratingForm = $this->createForm(RatingFormType::class, $actualite);
    $ratingForm->handleRequest($request);

    // Handle rating form submission
    if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        // Redirect back to the same page after editing the rating
        return $this->redirectToRoute('app_actualite_show_front', ['id' => $actualite->getId()]);
    }

    // Create the commentaire form
    $commentaire = new Commentaire();
    $commentaireForm = $this->createForm(CommentaireFormType::class, $commentaire, [
        'actualite' => $actualite,
    ]);
    $commentaireForm->handleRequest($request);

    // Handle commentaire form submission
    if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
        // Set the actualite for the commentaire
        $commentaire->setActualite($actualite);
        // Set the current date for the commentaire
        $commentaire->setDate(new \DateTime());

        // Persist the commentaire to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($commentaire);
        $entityManager->flush();

        // Redirect back to the same page after posting the commentaire
        return $this->redirectToRoute('app_actualite_show_front', ['id' => $actualite->getId()]);
    }
    

    // Fetch all commentaires related to the actualite
    $commentaires = $actualite->getCommentaires();
    $reponse = new Reponse();
    $reponseForm = $this->createForm(ReponseFormType::class, $reponse);
    $reponseForm->handleRequest($request);

    // Handle reponse form submission
    if ($reponseForm->isSubmitted() && $reponseForm->isValid()) {
        // Set the commentaire for the reponse
        $reponse->setCommentaire($commentaire); // You need to fetch the corresponding commentaire

        // Persist the reponse to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reponse);
        $entityManager->flush();

        // Redirect back to the same page after posting the reponse
        return $this->redirectToRoute('app_actualite_show_front', ['id' => $actualite->getId()]);
    }

    return $this->render('home_blog/blog.html.twig', [
        'actualite' => $actualite,
        'ratingForm' => $ratingForm->createView(),
        'commentaires' => $commentaires,
        'commentaireForm' => $commentaireForm->createView(),
        
    ]);
}

public function add(Request $request, int $commentaireId): Response
    {
        $commentaire = $this->getDoctrine()->getRepository(Commentaire::class)->find($commentaireId);
        
       
        $reponse = new Reponse();

        $reponseForm = $this->createForm(ReponseFormType::class, $reponse);
        $reponseForm->handleRequest($request);

        if ($reponseForm->isSubmitted() && $reponseForm->isValid()) {
            $reponse->setCommentaire($commentaire);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reponse);
            $entityManager->flush();
            return $this->redirectToRoute('app_actualite_show_front', ['id' => $commentaire->getActualite()->getId()]);
        }

        return $this->render('home_blog/addreponse.html.twig', [
            'commentaire' => $commentaire,
            'reponseForm' => $reponseForm->createView(),
        ]);
    }
    
}
