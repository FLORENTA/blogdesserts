<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="image_index", columns={"content"}),
 *         @ORM\Index(name="image_article", columns={"article_id"})
 *     }
 * )
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $src;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @var File
     */
    private $file;

    /**
     * @var \Datetime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="images")
     */
    private $article;

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->setUpdateAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(?string $src): self
    {
        $this->src = $src;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return Datetime
     */
    public function getUpdateAt(): Datetime
    {
        return $this->updateAt;
    }

    /**
     * @param Datetime $updateAt
     * @return Image
     */
    public function setUpdateAt(Datetime $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}
