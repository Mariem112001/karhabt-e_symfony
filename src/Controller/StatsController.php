<?php 
// src/Controller/StatsController.php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\CommentaireRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/admin/stats', name: 'app_stats')]
    public function index(ActualiteRepository $actualiteRepository, CommentaireRepository $commentaireRepository, ReponseRepository $reponseRepository): Response
    {
        // Get statistics data from repositories
        $actualiteCount = $actualiteRepository->count([]);
        $commentaireCount = $commentaireRepository->count([]);
        $reponseCount = $reponseRepository->count([]);

        // Render the statistics page with the data
        return $this->render('home_blog/stats.html.twig', [
            'actualiteCount' => $actualiteCount,
            'commentaireCount' => $commentaireCount,
            'reponseCount' => $reponseCount,
        ]);
    }
}
