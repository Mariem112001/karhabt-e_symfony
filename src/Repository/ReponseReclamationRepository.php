<?php
// src/Repository/ReponseReclamationRepository.php

namespace App\Repository;

use App\Entity\ReponseReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReponseReclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseReclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseReclamation[]    findAll()
 * @method ReponseReclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseReclamation::class);
    }

    // Ajoutez vos propres méthodes de requête personnalisées ici
}
