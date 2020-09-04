<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Exception\InvalidArgumentException;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param object $object
     * @return Comment[]
     */
    public function findAllForObject($object): array
    {
        if (!\is_object($object)) {
            throw new InvalidArgumentException('This method requires an object.');
        }

        return $this->createQueryBuilder('c')
            ->join('c.commentContext', 'comment_context')
            ->where('comment_context.class = :class')
            ->andWhere('comment_context.objectId = :object_id')
            ->andWhere('c.parent is NULL')
            ->setParameters([
                'class' => get_class($object),
                'object_id' => $object->getId(),
            ])
            ->getQuery()
            ->getResult()
       ;
    }

    /**
     * @return Comment[]
     */
    public function getList(): array
    {
        /** @var Comment[] */
        $comments = $this->findBy(['parent' => null,], ['createdAt' => 'DESC',]);
        foreach ($comments as $comment) {
            $commentContext = $comment->getCommentContext();
            $objectClass = $commentContext->getClass();
            $objectId = $commentContext->getObjectId();

            $object = $this->_em->getPartialReference($objectClass, $objectId);
            if (!\is_object($object)) {
                continue;
            }
            $commentContext->setObject($object);
        }

        return $comments;
    }
}
