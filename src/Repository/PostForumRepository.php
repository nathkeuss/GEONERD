<?php

namespace App\Repository;

use App\Entity\PostForum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostForum>
 */
class PostForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostForum::class);
    }

    public function findMainPosts(): array
    {
        return $this->createQueryBuilder('post')
            ->where('post.parentPost IS NULL') // Filtre les posts qui n'ont pas de parent
            ->orderBy('post.datePost', 'DESC') // Trier par date décroissante, par exemple
            ->getQuery()
            ->getResult();
    }
}
