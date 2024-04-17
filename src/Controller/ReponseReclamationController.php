<?php

namespace App\Controller;

use App\Entity\ReponseReclamation;
use App\Form\ReponseReclamation1Type;
use App\Repository\ReponseReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reclamation;

#[Route('/reponse/reclamation')]
class ReponseReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reponse_reclamation_index', methods: ['GET'])]
    public function index(ReponseReclamationRepository $reponseReclamationRepository): Response
    {
        return $this->render('reponse_reclamation/index.html.twig', [
            'reponse_reclamations' => $reponseReclamationRepository->findAll(),
        ]);
    }

    #[Route('/new/{idr}', name: 'app_reponse_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, $idr): Response
{
    // Récupérer la réclamation correspondante
    $reclamation = $entityManager->getRepository(Reclamation::class)->find($idr);
    
    // Vérifier si la réclamation existe
    if (!$reclamation) {
        throw $this->createNotFoundException('La réclamation correspondante n\'a pas été trouvée');
    }

    $reponseReclamation = new ReponseReclamation();
    $reponseReclamation->setReclamation($reclamation);
    $reponseReclamation->setDateReponseR(new \DateTime('now'));

    $form = $this->createForm(ReponseReclamation1Type::class, $reponseReclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
         
        $existingReponse = $entityManager->getRepository(ReponseReclamation::class)->findOneBy([
            'contenuReponse' => $reponseReclamation->getContenureponse(),
            'reclamation' => $reponseReclamation->getReclamation(),
            // Ajoutez d'autres champs ici pour la comparaison
        ]);
        
        if ($existingReponse) {
            // Réclamation existante trouvée, afficher un message d'erreur ou rediriger l'utilisateur
            // Vous pouvez personnaliser cette partie selon vos besoins
            $this->addFlash('error', 'vous avez repondu a cette réclamation avec la meme reponse');
        } else {
            // Persister la réclamation dans la base de données
            $entityManager->persist($reponseReclamation);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre réponse a été soumise avec succès !');

       

        return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
    }
    return $this->renderForm('reponse_reclamation/new.html.twig', [
        'reponse_reclamation' => $reponseReclamation,
        'form' => $form,
    ]);
}



#[Route('/{idreponser}', name: 'app_reponse_reclamation_show', methods: ['GET'])]
public function show(ReponseReclamation $reponseReclamation): Response
{
    return $this->render('reponse_reclamation/show.html.twig', [
        'reponse_reclamation' => $reponseReclamation,
    ]);
}

    #[Route('/{idreponser}/edit', name: 'app_reponse_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReponseReclamation $reponseReclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseReclamation1Type::class, $reponseReclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse_reclamation/edit.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idreponser}', name: 'app_reponse_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, ReponseReclamation $reponseReclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponseReclamation->getIdreponser(), $request->request->get('_token'))) {
            $entityManager->remove($reponseReclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
