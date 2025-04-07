<?php

namespace App\Repository;

use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function searchByTopicTitle(string $search): array
    {
        $qb = $this->createQueryBuilder('topic');

        $query = $qb->select('topic')
            ->where('topic.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();

        return $query->getResult();

    }

    public function findUserTopicsOrderedByMostRecentFirst(int $userId): array {
        return $this->createQueryBuilder('topic')
            ->where('topic.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('topic.date', 'DESC')
            ->getQuery()
            ->getResult();
    }


}
