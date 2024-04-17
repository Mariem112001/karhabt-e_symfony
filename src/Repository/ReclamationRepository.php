<?php


// src/Repository/ReclamationRepository.php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    // Méthode pour récupérer les statistiques du nombre de réclamations par date de réclamation
    public function getReclamationsByDate(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.dateReclamation as date, COUNT(r.idr) as count')
            ->groupBy('r.dateReclamation')
            ->getQuery()
            ->getResult();
    }
    
}
