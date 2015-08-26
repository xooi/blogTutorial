<?php
// src/Blogger/BlogBundle/Repository/CommentRepository.php

namespace Blogger\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 *
 * Esta clase fue generada por el ORM de Doctrine(NOOO, LO HICE YOOOOO). Abajo añade
 * tu propia personalización a los métodos del repositorio.
 */
class CommentRepository extends EntityRepository
{
    public function getCommentsForBlog($blogId, $approved = true)
    {
        $qb = $this->createQueryBuilder('c')
                   ->select('c')
                   ->where('c.blog = :blog_id')
                   ->addOrderBy('c.created')
                   ->setParameter('blog_id', $blogId);

           if (false === is_null($approved)) {
            $qb->andWhere('c.approved = :approved')
                    ->setParameter('approved', $approved);
        }

        return $qb->getQuery()
                  ->getResult();
    }
    
    public function getLatestComments($limit = 10)
    {
    $qb = $this->createQueryBuilder('c')
                ->select('c')
                ->addOrderBy('c.id', 'DESC');

    if (false === is_null($limit))
        $qb->setMaxResults($limit);

    return $qb->getQuery()
              ->getResult();
    }
}