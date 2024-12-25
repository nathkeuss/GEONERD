<?php

namespace App\Repository;

use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tutorial>
 */
class TutorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorial::class);
    }

    public function findByTutorialTitle(string $search): array
    {
        $qb = $this->createQueryBuilder('tutorial');

        $query = $qb->select('tutorial')
            ->where('tutorial.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();

        return $query->getResult();

    }
}
