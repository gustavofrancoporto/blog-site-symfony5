<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findUnseenByUser(User $user)
    {
        return $this->createQueryBuilder('n')
            ->select('count(n)')
            ->where('n.user = :user')
            ->andWhere('n.seen = :seen')
            ->setParameter('user', $user)
            ->setParameter('seen', false)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function markAllAsReadByUser(User $user)
    {
        return $this->createQueryBuilder('n')
            ->update(Notification::class, 'n')
            ->set('n.seen', true)
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
