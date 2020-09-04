<?php

namespace App\Entity;

use App\Repository\CommentContextRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentContextRepository::class)
 */
class CommentContext
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $objectId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $class;

    /**
     * @ORM\OneToOne(targetEntity="Comment", mappedBy="commentContext")
     */
    private $comment;
    /**
     * @var $object
     */
    private $object;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setObjectId(int $objectId): self
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        // set (or unset) the owning side of the relation if necessary
        $newCommentContext = null === $comment ? null : $this;
        if ($comment->getCommentContext() !== $newCommentContext) {
            $comment->setCommentContext($newCommentContext);
        }

        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object|null $object
     * @return CommentContext
     */
    public function setObject($object): self
    {
        $this->object = $object;

        return $this;
    }
}
