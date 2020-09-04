<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\CommentContext;
use App\Repository\CommentContextRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;

class CommentService
{
    private $em;
    /**
     * @var CommentContextRepository
     */
    private $commentContextRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em = $entityManager;
        $this->commentContextRepository = $entityManager->getRepository(CommentContext::class);
        $this->logger = $logger;
    }

    /**
     * @param Comment[] $comments
     */
    public function addObject(array $comments)
    {
//        foreach ($comments as $comment) {
//            $commentContext = $comment->getCommentContext();
//            $objectClass = $commentContext->getClass();
//            $objectId = $commentContext->getObjectId();
//
//            $object = $this->em->getPartialReference($objectClass, $objectId);
//            if (!\is_object($object)) {
//                continue;
//            }
//            $commentContext->setObject($object);
//        }
    }

    /**
     * @param object $object
     * @return int
     */
    public function getNumberOfVisibleCommentsWithNoParentForObject($object): int
    {
        try {
            $number = $this->commentContextRepository->getNumberOfVisibleCommentsWithNoParentForObject($object);
        } catch (NoResultException|NonUniqueResultException $exception) {
            $number = 0;
            $this->logger->error($exception->getMessage(), [
                'method' => __METHOD__
            ]);
        }

        return $number;
    }
}