<?php

namespace App\Controller;

use App\Entity\Messagerie;
use App\Form\Messagerie1Type;
use App\Repository\MessagerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;




#[Route('/messagerie')]
class MessagerieClientController extends AbstractController


{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private function checkForBadWords(string $text): bool
    {
        $url = 'https://api.api-ninjas.com/v1/profanityfilter';
        $apiKey = 'FVFAvAF9dOXLIvNjA2LY6Jdg8TCKcQhE3WrzT3YX';  // Remplacez 'YOUR_API_KEY_HERE' par votre clé API réelle

        $response = $this->client->request('GET', $url, [
            'query' => ['text' => $text],
            'headers' => ['X-Api-Key' => $apiKey]
        ]);

        $content = $response->getContent();
        $result = json_decode($content, true);

        return $result['has_profanity'] ?? false;
    }
    #[Route('/client', name: 'app_messagerie_indexclient', methods: ['GET'])]
    public function index(SecurityController $security,MessagerieRepository $messagerieRepository): Response
    {
        $user = $security->getUser();

        // Fetch reservations for the current user
        $messageriesQuery = $messagerieRepository->findBy(['sender' => $user]);

        return $this->render('messagerie/indexClient.html.twig', [
            'messageries' => $messageriesQuery,
        ]);
    }
    

    #[Route('/newclient', name: 'app_messagerie_newclient', methods: ['GET', 'POST'])]
    public function new(SecurityController $security,Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll(); // Fetch all users
        
        // Create a new instance of Messagerie
        $messagerie = new Messagerie();
        $messagerie->setSender($security->getUser()); // Set the sender to the current user
        $messagerie->setDateEnvoie(new \DateTime());
        $messagerie->setVu(false);
        $messagerie->setDeleted(false);
    
        $form = $this->createForm(Messagerie1Type::class, $messagerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $receiver = $form->get('receiver')->getData();
            $messagerie->setReceiver($receiver);

            if ($this->checkForBadWords($messagerie->getContenu())) {
                $this->addFlash('error', 'Votre message contient des mots inappropriés.');
                return $this->redirectToRoute('app_messagerie_newclient');
            }

            $entityManager->persist($messagerie);
            $entityManager->flush();

            return $this->redirectToRoute('app_messagerie_indexclient');
        }

        return $this->renderForm('messagerie/newClient.html.twig', [
            'messagerie' => $messagerie,
            'form' => $form,
            'users' => $users,
        ]);
    }


    #[Route('/client{idmessage}', name: 'app_messagerie_showclient', methods: ['GET'])]
    public function show(Messagerie $messagerie): Response
    {
        return $this->render('messagerie/showClient.html.twig', [
            'messagerie' => $messagerie,
        ]);
    }

    #[Route('/{idmessage}/editclient', name: 'app_messagerie_editclient', methods: ['GET', 'POST'])]
    public function edit(Request $request, Messagerie $messagerie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Messagerie1Type::class, $messagerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_messagerie_indexclient', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messagerie/editClient.html.twig', [
            'messagerie' => $messagerie,
            'form' => $form,
        ]);
    }

    #[Route('/{idmessage}', name: 'app_messagerie_delete', methods: ['POST'])]
    public function delete(Request $request, Messagerie $messagerie, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('delete' . $messagerie->getIdmessage(), $request->request->get('_token'))) {
            // Marquer le message comme supprimé en définissant la propriété deleted sur true
            $messagerie->setDeleted(true);
            $entityManager->flush();
        }
    
        // Rediriger vers la page de la conversation avec un message de confirmation
        $this->addFlash('success', 'Message marqué comme supprimé avec succès.');
    
        return $this->redirectToRoute('conversation_view', [
            'senderId' => $messagerie->getSender()->getIdu(),
            'receiverId' => $messagerie->getReceiver()->getIdu(),
        ]);
    }
    
    #[Route('/conversationclient/{senderId}/{receiverId}', name: 'conversation_viewclient')]
    public function viewConversation(Request $request, MessagerieRepository $messagerieRepository, $senderId, $receiverId, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les messages entre l'expéditeur et le destinataire
        $messageries = $messagerieRepository->findBySenderAndReceiver($senderId, $receiverId);
    
        // Récupérer les informations sur l'utilisateur receiver
        $receiver = $entityManager->getRepository(User::class)->find($receiverId);
    
        // Passer l'URL de l'image de l'utilisateur à la vue
        $imageUserUrl = $receiver ? $receiver->getImageUser() : null;
    
        $messagerie = new Messagerie();
        $messagerie->setSender($entityManager->getReference(User::class, $senderId));
        $messagerie->setReceiver($receiver);
        $messagerie->setDateenvoie(new \DateTime());
    
        // Créer le formulaire
        $form = $this->createForm(Messagerie1Type::class, $messagerie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($messagerie);
            $entityManager->flush();
        }
    
        return $this->render('messagerie/conversationClient.html.twig', [
            'messageries' => $messageries,
            'receiver' => $receiver,
            'imageUserUrl' => $imageUserUrl, // Passer l'URL de l'image à la vue
            'form' => $form->createView(),
        ]);
    }
    
}
