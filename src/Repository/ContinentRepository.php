<?php

namespace App\Repository;

use App\Entity\Continent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Continent>
 */
class ContinentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Continent::class);
    }

    public function findByContinentName(string $search): array
    {
        $qb = $this->createQueryBuilder('continent');

        $query = $qb->select('continent')
                    ->where('continent.name LIKE :search')
                    ->setParameter('search', '%' . $search . '%')
                    ->getQuery();

        return $query->getResult();

    }
}
