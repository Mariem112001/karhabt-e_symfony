<?php


// src/Repository/ReclamationRepository.php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    // Méthode pour récupérer les statistiques du nombre de réclamations par date de réclamation


    public function getReclamationsByDate(): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('count', 'count');
    
        $query = $this->getEntityManager()->createNativeQuery('
            SELECT DATE_FORMAT(r.dateReclamation, \'%Y-%m-%d\') as date, COUNT(r.idr) as count
            FROM reclamation r
            GROUP BY r.dateReclamation
        ', $rsm);
    
        return $query->getResult();
    }
    
}
