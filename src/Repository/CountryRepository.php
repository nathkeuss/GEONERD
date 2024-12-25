<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function findByCountryName(string $search): array
    {
        $qb = $this->createQueryBuilder('country');

        $query = $qb->select('country')
            ->where('country.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();

        return $query->getResult();

    }
}
