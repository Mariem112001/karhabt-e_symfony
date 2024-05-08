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
use App\Repository\ArrivageRepository;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Security\Core\Security;


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
    #[Route('/Trie', name: 'app_arrivage_index_Trie', methods: ['GET'])]
    public function indexTrieDate(EntityManagerInterface $entityManager, ArrivageRepository $arrivageRepository): Response
    {
        $arrivages = $arrivageRepository->findByDateentree();
    
        return $this->render('arrivage/index.html.twig', [
            'arrivages' => $arrivages,
        ]);
    }

    #[Route('/new', name: 'app_arrivage_new', methods: ['GET', 'POST'])]
    public function new(Security $security, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $arrivage = new Arrivage();
        $form = $this->createForm(ArrivageType::class, $arrivage);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $arrivage->setUser($user);
            $entityManager->persist($arrivage);
            $entityManager->flush();
            $details = [
                'quantity' => $arrivage->getQuantite(),
                'dateEntree' => $arrivage->getDateEntree(),
            ];
    
            $voiture = $arrivage->getVoiture();
            $voitureId = $voiture->getIdv();
            
            // Envoyer l'email avec les détails de la nouvelle arrivée
            $this->sendEmail($security, $mailer, $entityManager, $details, $voitureId);
    
            return $this->redirectToRoute('app_arrivage_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('arrivage/new.html.twig', [
            'arrivage' => $arrivage,
            'form' => $form,
        ]);
    }
    

    #[Route('/show', name: 'app_arrivage_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager): Response
    {
        $arrivages = $entityManager
            ->getRepository(Arrivage::class)
            ->findAll();

        return $this->render('arrivage/show.html.twig', [
            'arrivages' => $arrivages,
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

  /*  #[Route('/email', name: 'app_email')]
public function sendEmail(MailerInterface $mailer, EntityManagerInterface $entityManager, $details, int $voitureId)
{
    $transport = Transport::fromDsn('smtp://davincisdata@gmail.com:vjyyzltfspajsbpf@smtp.gmail.com:587');
    $mailer = new Mailer($transport);

    // Récupérer la marque et le modèle de la voiture à partir de son ID
    $voitureDetails = $this->getDetailsFromId($entityManager, $voitureId);
    $marque = $voitureDetails['marque'] ?? 'Marque inconnue';
    $modele = $voitureDetails['modele'] ?? 'Modèle inconnu';

    // Construire le contenu personnalisé du mail avec les détails de la nouvelle arrivée, la marque et le modèle de la voiture
    $mailContent = "un arrivage est ajouté avec ces details :\n";
    $mailContent .= "Quantité : " . $details['quantity'] . "\n";
    $mailContent .= "Date d'entrée : " . $details['dateEntree']->format('Y-m-d') . "\n";
    $mailContent .= "Marque de la voiture : " . $marque . "\n";
    $mailContent .= "Modèle de la voiture : " . $modele . "\n";

    // Créer l'email
    $email = (new Email())
        ->from('davincisdata@gmail.com')
        ->to('yassineanater97@gmail.com')
        ->subject('Notification de commentaire')
        ->text($mailContent)
        ->html('<p>' . $mailContent . '</p>');

    // Envoyer l'email
    $mailer->send($email);
}*/


#[Route('/email', name: 'app_email')]
public function sendEmail(Security $security, MailerInterface $mailer, EntityManagerInterface $entityManager, $details, int $voitureId): Response
{
    // Get the current logged-in user
    $user = $security->getUser();
    $userEmail = $user->getEmail();

    // Initialize the transport (SMTP in this case)
    $transport = Transport::fromDsn('smtp://davincisdata@gmail.com:vjyyzltfspajsbpf@smtp.gmail.com:587');
    $mailer = new Mailer($transport);
    
    // Get the details of the car from its ID
    $voitureDetails = $this->getDetailsFromId($entityManager, $voitureId);
    $marque = $voitureDetails['marque'] ?? 'Unknown Marque';
    $modele = $voitureDetails['modele'] ?? 'Unknown Model';

    // Build the custom email content with the details of the new arrival, car brand, and model
    $mailContent = "A new arrival is added with these details:\n";
    $mailContent .= "Quantity: " . $details['quantity'] . "\n";
    $mailContent .= "Entry Date: " . $details['dateEntree']->format('Y-m-d') . "\n";
    $mailContent .= "Car Brand: " . $marque . "\n";
    $mailContent .= "Car Model: " . $modele . "\n";

    // Create the email
    $email = (new Email())
        ->from('davincisdata@gmail.com')
        ->to($userEmail) // Send the email to the logged-in user
        ->subject('New Arrival Notification')
        ->text($mailContent)
        ->html('<p>' . $mailContent . '</p>');

    // Send the email
    $mailer->send($email);

    // Return a response
    return new Response('Email sent successfully!');
}

// Méthode pour récupérer la marque et le modèle de la voiture à partir de son ID
public function getDetailsFromId(EntityManagerInterface $entityManager, int $id): ?array
{
    // Récupérer l'entité Voiture à partir de son ID
    $voiture = $entityManager->getRepository(Voiture::class)->find($id);

    // Vérifier si une voiture avec cet ID existe
    if ($voiture) {
        // Retourner la marque et le modèle de la voiture
        return [
            'marque' => $voiture->getMarque(),
            'modele' => $voiture->getModele(),
        ];
    } else {
        // Si aucune voiture avec cet ID n'est trouvée, retourner null ou une autre valeur par défaut selon vos besoins
        return null;
    }
}


#[Route("/stats", name:'stats')]
public function statistiques(ArrivageRepository $arrivageRepository): Response
{
    // Récupérer tous les arrivages
    $arrivages = $arrivageRepository->findAll();

    // Initialiser un tableau pour stocker les statistiques
    $stats = [];

    // Calculer les statistiques pour chaque modèle de voiture
    foreach ($arrivages as $arrivage) {
        $voiture = $arrivage->getVoiture();
        $modele = $voiture->getModele();
        $quantite = $arrivage->getQuantite();

        // Ajouter la quantité à la statistique du modèle de voiture
        if (!isset($stats[$modele])) {
            $stats[$modele] = 0;
        }
        $stats[$modele] += $quantite;
    }

    return $this->render('arrivage/chart.html.twig', [
        'stats' => json_encode($stats)
    ]);
}


}
