<?php

namespace App\EventListener;

use App\Entity\Image;
use App\Service\FileUploader;

/**
 * Class ImageListener
 * @package App\EventListener
 */
class ImageListener
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var string
     */
    private $imagePath;

    /**
     * ImageListener constructor.
     * @param FileUploader $fileUploader
     * @param $imagePath
     */
    public function __construct(FileUploader $fileUploader, $imagePath)
    {
        $this->fileUploader = $fileUploader;
        $this->imagePath = $imagePath;
    }

    /**
     * @param Image $image
     */
    public function prePersist(Image $image): void
    {
        $this->upload($image);
    }

    /**
     * @param Image $image
     */
    public function preUpdate(Image $image): void
    {
        $src = $image->getSrc();
        $file = $image->getFile();
        // Loading a new image, removing the old one
        if (null !== $src && null !== $file) {
            $this->fileUploader->remove($this->imagePath.DIRECTORY_SEPARATOR.$src);
        }
        $this->upload($image);
    }

    public function postRemove(Image $image)
    {
        $src = $image->getSrc();
        if (null !== $src) {
            $this->fileUploader->remove($this->imagePath.DIRECTORY_SEPARATOR.$src);
        }
    }

    /**
     * @param Image $image
     */
    private function upload(Image $image): void
    {
        $filename = $this->fileUploader->upload($image->getFile(), $this->imagePath);
        if (null !== $filename) {
            $image->setAlt($image->getArticle()->getTitle());
            $image->setSrc($filename);
        }
    }
}