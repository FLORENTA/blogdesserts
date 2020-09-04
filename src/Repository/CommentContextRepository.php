<?php

namespace App\Repository;

use App\Entity\CommentContext;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentContext|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentContext|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentContext[]    findAll()
 * @method CommentContext[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentContextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentContext::class);
    }

    /**
     * @param $object
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getNumberOfVisibleCommentsWithNoParentForObject($object)
    {
        $qb = $this->createQueryBuilder('cc')
            ->select('COUNT(cc.id)')
            ->join('cc.comment', 'comment')
            ->where('cc.class = :class')
            ->andWhere('cc.objectId = :object_id')
            ->andWhere('comment.visible = true')
            ->andWhere('comment.parent is null')
            ->setParameters([
                'class' => get_class($object),
                'object_id' => $object->getId(),
            ]);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
