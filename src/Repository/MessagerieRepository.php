<?php

// src/Repository/MessagerieRepository.php

namespace App\Repository;

use App\Entity\Messagerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Messagerie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messagerie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messagerie[]    findAll()
 * @method Messagerie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messagerie::class);
    }

    /**
     * @param int $senderId
     * @param int $receiverId
     * @return Messagerie[] Returns an array of Messagerie objects
     */
    public function findBySenderAndReceiver(int $senderId, int $receiverId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.receiver = :receiverId')
            ->setParameter('senderId', $senderId)
            ->setParameter('receiverId', $receiverId)
            ->getQuery()
            ->getResult();
    }

  /**
     * @param int $senderId
     * @return Messagerie[] Returns an array of Messagerie objects
     */
    public function findBySenderId(int $senderId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :senderId')
            ->setParameter('senderId', $senderId)
            ->getQuery()
            ->getResult();
    }
}
