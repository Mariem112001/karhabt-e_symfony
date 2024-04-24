<?php
// src/Repository/ReponseReclamationRepository.php

namespace App\Repository;

use App\Entity\ReponseReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReponseReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseReclamation::class);
    }

    /**
     * Récupère la réponse associée à une réclamation en fonction de son identifiant.
     *
     * @param int $idR L'identifiant de la réclamation
     * @return ReponseReclamation|null La réponse associée à la réclamation, ou null si aucune réponse n'est trouvée
     */
    public function findResponsesByReclamationId(int $idR): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.reclamation = :idR')
            ->setParameter('idR', $idR)
            ->getQuery()
            ->getResult();
    }
}
