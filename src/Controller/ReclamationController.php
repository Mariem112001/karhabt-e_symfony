<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Asset;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use DateTimeInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use DateTime; 
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ReclamationRepository;





#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
  

    #[Route('/', name: 'app_reclamation_index', methods: ['GET', 'POST'])]
 // Dans votre contrôleur ReclamationController


public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
{
    // Initialiser la requête pour récupérer les réclamations
    $queryBuilder = $entityManager->getRepository(Reclamation::class)->createQueryBuilder('r');

    // Filtrer les réclamations par date de réclamation si une date est sélectionnée dans le formulaire
    if ($dateReclamation = $request->query->get('dateReclamation')) {
        $date = new DateTime($dateReclamation);
        $queryBuilder
            ->andWhere('r.dateReclamation = :date')
            ->setParameter('date', $date);
    }

    // Filtrer les réclamations par ID utilisateur (24)
    $queryBuilder
        ->andWhere('r.user = :userId')
        ->setParameter('userId', 28);

    // Créer la requête
    $query = $queryBuilder->orderBy('r.dateReclamation', 'DESC')->getQuery();

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        5
    );

    // Passer une variable indiquant si des réclamations ont été trouvées
    $noReclamationsFound = ($pagination->getTotalItemCount() === 0);

    // Rendre la vue avec les résultats paginés et le formulaire de recherche
    return $this->render('reclamation/index.html.twig', [
        'pagination' => $pagination,
        'noReclamationsFound' => $noReclamationsFound,
    ]);
}

    
    
#[Route('/all', name: 'app_reclamation_index_all', methods: ['GET', 'POST'])]
public function all(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
{
    // Initialiser la requête pour récupérer les réclamations
    $queryBuilder = $entityManager->getRepository(Reclamation::class)->createQueryBuilder('r');

    // Filtrer les réclamations par date de réclamation si une date est passée en paramètre GET
    if ($dateReclamation = $request->query->get('dateReclamation')) {
        $date = new DateTime($dateReclamation);
        $queryBuilder
            ->andWhere('r.dateReclamation = :date')
            ->setParameter('date', $date);
    }

    // Créer la requête
    $query = $queryBuilder->orderBy('r.dateReclamation', 'DESC')->getQuery();

    // Paginer les résultats
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        8
    );

    // Passer une variable indiquant si des réclamations ont été trouvées
    $noReclamationsFound = ($pagination->getTotalItemCount() === 0);

    // Rendre la vue avec les résultats paginés et la variable noReclamationsFound
    return $this->render('reponse_reclamation/indexAll.html.twig', [
        'pagination' => $pagination,
        'noReclamationsFound' => $noReclamationsFound,
    ]);
}






    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $idu = 28; // Vous avez mentionné que vous souhaitez utiliser l'ID utilisateur 24 statiquement

$user = $entityManager->getRepository(User::class)->find($idu); // Recherche de l'utilisateur correspondant à l'ID 24

if (!$user) {
    throw new \Exception("L'utilisateur avec l'ID $idu n'a pas été trouvé."); // Gérer le cas où l'utilisateur n'est pas trouvé
}

$reclamation = new Reclamation();
$reclamation->setUser($user);
        // Créer une nouvelle instance de Reclamation
       
        
        // Attribuer la date actuelle à la propriété dateReclamation
        $reclamation->setDateReclamation(new \DateTime());

        //$idu = $request->query->get('idu'); // Récupérer la valeur de 'idu' dans l'URL
//$user = $entityManager->getRepository(User::class)->find($idu); // Recherche de l'utilisateur par son identifiant

//$email = ''; // Initialiser la variable email
//if ($user) {
   // $email = $user->getEmail(); // Récupérer l'email de l'utilisateur si trouvé
//}

    
      ; // Recherche de l'utilisateur par son identifiant
        
        $email = ''; // Initialisation de la variable email
        if ($user) {
            $email = $user->getEmail(); // Récupérer l'email de l'utilisateur si trouvé
        }
        
    
        // Créer le formulaire
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
    
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {

            
            $existingReclamation = $entityManager->getRepository(Reclamation::class)->findOneBy([
                'sujet' => $reclamation->getSujet(),
                'description' => $reclamation->getDescription(),
                // Ajoutez d'autres champs ici pour la comparaison
            ]);
            
            if ($existingReclamation) {
                // Réclamation existante trouvée, afficher un message d'erreur ou rediriger l'utilisateur
                // Vous pouvez personnaliser cette partie selon vos besoins
                $this->addFlash('error', 'Une réclamation avec les mêmes données existe déjà.');
            } else {
                // Persister la réclamation dans la base de données
                $entityManager->persist($reclamation);
                $entityManager->flush();
        
                $this->addFlash('success', 'Votre réclamation a été soumise avec succès !');

                // Rediriger l'utilisateur vers la page d'index des réclamations
                return $this->redirectToRoute('app_reclamation_new');
            }
        }
    
        // Rendre le formulaire et la page de création de réclamation
        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idr}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/all/{idr}', name: 'app_reclamation_showR', methods: ['GET'])]
    public function showR(Reclamation $reclamation): Response
    {
        return $this->render('reponse_reclamation/showReclamation.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    #[Route('/{idr}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idr}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdr(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/reclamation/statistics', name: 'app_reclamation_statistics')]
    public function statistics(ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer les données pour les statistiques
        $statisticsData = $reclamationRepository->getReclamationsByDate();

        // Rendre la vue avec les données des statistiques
        return $this->render('reponse_reclamation/statistics.html.twig', [
            'statisticsData' => $statisticsData,
        ]);
    }
    

}