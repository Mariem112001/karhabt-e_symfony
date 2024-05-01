<?php
namespace App\Repository;

use App\Entity\Arrivage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArrivageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Arrivage::class);
    }

    /**
     * @return Arrivage[] Returns an array of Arrivage objects sorted by dateEntree
     */
    public function findByDateentree()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.dateentree', 'ASC')
            ->getQuery()
            ->getResult();
    }
}