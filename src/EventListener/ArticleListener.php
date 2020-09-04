<?php

namespace App\EventListener;

use App\Entity\Article;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ArticleListener
 * @package App\EventListener
 */
class ArticleListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var string
     */
    private $pdfPath;

    /**
     * ArticleListener constructor.
     * @param EntityManagerInterface $entityManager
     * @param FileUploader $fileUploader
     * @param string $pdfPath
     */
    public function __construct(EntityManagerInterface $entityManager, FileUploader $fileUploader, string $pdfPath)
    {
        $this->em = $entityManager;
        $this->fileUploader = $fileUploader;
        $this->pdfPath = $pdfPath;
    }

    /**
     * @param Article $article
     */
    public function prePersist(Article $article): void
    {
        $article->setCreatedAt(new DateTime());
        $this->upload($article);
    }

    /**
     * @param Article $article
     */
    public function preUpdate(Article $article): void
    {
        /** @var string|null $pdf */
        $pdf = $article->getPdf();
        /** @var File|null $file */
        $file = $article->getFile();
        if (null !== $pdf && null !== $file) {
            $this->fileUploader->remove($this->pdfPath.DIRECTORY_SEPARATOR.$pdf);
        }
        $this->upload($article);
    }

    public function postRemove(Article $article): void
    {
        $pdf = $article->getPdf();
        if (null !== $pdf) {
            $this->fileUploader->remove($this->pdfPath.DIRECTORY_SEPARATOR.$pdf);
        }
    }

    /**
     * @param Article $article
     */
    private function upload(Article $article): void
    {
        /** @var string|null */
        $fileName = $this->fileUploader->upload($article->getFile(), $this->pdfPath);
        if (null !== $fileName) {
            $article->setPdf($fileName);
        }
    }
}