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

#[Route('/messagerie')]
class MessagerieController extends AbstractController
{
    #[Route('/', name: 'app_messagerie_index', methods: ['GET'])]
    public function index(MessagerieRepository $messagerieRepository): Response
    {
        $messageries = $messagerieRepository->findBySenderId(62); // Remplacez 62 par l'ID du receiver souhaité

        return $this->render('messagerie/index.html.twig', [
            'messageries' => $messageries,
        ]);
    }

    #[Route('/new', name: 'app_messagerie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idus = 62; // Vous avez mentionné que vous souhaitez utiliser l'ID utilisateur 24 statiquement
        $idur = 28;

        $users = $entityManager->getRepository(User::class)->find($idus); // Recherche de l'utilisateur correspondant à l'ID 24
        $userr = $entityManager->getRepository(User::class)->find($idur);
        
        if (!$users) {
            throw new \Exception("L'utilisateur avec l'ID $idus n'a pas été trouvé."); // Gérer le cas où l'utilisateur n'est pas trouvé
        }
        if (!$userr) {
            throw new \Exception("L'utilisateur avec l'ID $idur n'a pas été trouvé."); // Gérer le cas où l'utilisateur n'est pas trouvé
        }
        
        $messagerie = new Messagerie();
        $messagerie -> setSender($users);
        $messagerie -> setReceiver($userr);
                // Créer une nouvelle instance de Reclamation
               
                
                // Attribuer la date actuelle à la propriété dateReclamation
                $messagerie ->setDateenvoie(new \DateTime());
                $messagerie ->setVu(true);
                $messagerie ->setDeleted(false);
        
                //$idu = $request->query->get('idu'); // Récupérer la valeur de 'idu' dans l'URL
        //$user = $entityManager->getRepository(User::class)->find($idu); // Recherche de l'utilisateur par son identifiant
        
        //$email = ''; // Initialiser la variable email
        //if ($user) {
           // $email = $user->getEmail(); // Récupérer l'email de l'utilisateur si trouvé
        //}
        
            
              ; // Recherche de l'utilisateur par son identifiant
                
            
      
        $form = $this->createForm(Messagerie1Type::class, $messagerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($messagerie);
            $entityManager->flush();

            return $this->redirectToRoute('app_messagerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messagerie/new.html.twig', [
            'messagerie' => $messagerie,
            'form' => $form,
        ]);
    }

    #[Route('/{idmessage}', name: 'app_messagerie_show', methods: ['GET'])]
    public function show(Messagerie $messagerie): Response
    {
        return $this->render('messagerie/show.html.twig', [
            'messagerie' => $messagerie,
        ]);
    }

    #[Route('/{idmessage}/edit', name: 'app_messagerie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Messagerie $messagerie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Messagerie1Type::class, $messagerie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_messagerie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messagerie/edit.html.twig', [
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
    
    #[Route('/conversation/{senderId}/{receiverId}', name: 'conversation_view')]
public function viewConversation(Request $request, MessagerieRepository $messagerieRepository, $senderId, $receiverId,EntityManagerInterface $entityManager): Response
{
    // Récupérer les messages entre l'expéditeur et le destinataire
    $messageries = $messagerieRepository->findBySenderAndReceiver($senderId, $receiverId);

   
        $users = $entityManager->getRepository(User::class)->find($senderId); // Recherche de l'utilisateur correspondant à l'ID 24
        $userr = $entityManager->getRepository(User::class)->find($receiverId);
    $messagerie = new Messagerie();
    $messagerie -> setSender($users);
    $messagerie -> setReceiver($userr);
    $messagerie ->setDateenvoie(new \DateTime());
        
    //$idu = $request->query->get('idu'); // Récupérer la valeur de 'idu' dans l'URL
//$user = $entityManager->getRepository(User::class)->find($idu); // Recherche de l'utilisateur par son identifiant

//$email = ''; // Initialiser la variable email
//if ($user) {
// $email = $user->getEmail(); // Récupérer l'email de l'utilisateur si trouvé
//}


  ; // Recherche de l'utilisateur par son identifiant
    


$form = $this->createForm(Messagerie1Type::class, $messagerie);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$entityManager->persist($messagerie);
$entityManager->flush();

}

    return $this->render('messagerie/conversation.html.twig', [
        'messageries' => $messageries,
        'form' => $form->createView(), // Passer le formulaire à la vue
    ]);
}
}
