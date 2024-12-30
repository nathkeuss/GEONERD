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
            ->where('post.parentPost IS NULL') // filtre les posts qui n'ont pas de parent
            ->orderBy('post.datePost', 'DESC') // trier par date décroissante
            ->getQuery()
            ->getResult();
    }

    public function findByTitleOrUsername(string $search): array
    {
        $qb = $this->createQueryBuilder('post');

        $query = $qb->select('post')
            ->leftJoin('post.user', 'user') // joint l'entité User pour accéder au username
            ->where('post.parentPost IS NULL') // exclut les réponses (prends que les main posts)
            ->andWhere('post.title LIKE :search OR user.username LIKE :search') // rechercher dans le titre ou le username
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('post.datePost', 'DESC') // trier par date décroissante
            ->getQuery();

        return $query->getResult();
    }


}
