<?php

namespace App\Repository;

use App\Entity\Reply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reply>
 *
 * @method Reply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reply[]    findAll()
 * @method Reply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reply::class);
    }

    public function findUserRepliesOrderedByMostRecentFirst(int $userId): array
    {
        return $this->createQueryBuilder('reply')
            ->where('reply.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('reply.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
