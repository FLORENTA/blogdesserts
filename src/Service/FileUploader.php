<?php

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FileUploader constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param File|null $file
     * @param string $path
     * @return string|null
     */
    public function upload(?File $file, string $path): ?string
    {
        if (null !== $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            $fs = new Filesystem();

            try {
                $fs->mkdir($path);
                $file->move($path, $newFilename);
            } catch (IOException $IOException) {
                $this->logger->error(sprintf('Could not create directory %s', $path), [
                    'method' => __METHOD__
                ]);
            } catch (FileException $e) {
                $this->logger->error(sprintf('Could not create file %s in directory %s', $newFilename, $path), [
                    'method' => __METHOD__
                ]);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    'method' => __METHOD__
                ]);
            }

            return $newFilename;
        }

        return null;
    }

    /**
     * @param string $path
     */
    public function remove(string $path): void
    {
        $fs = new Filesystem();
        try {
            $fs->remove($path);
        } catch (IOException $IOException) {
            $this->logger->error(sprintf('Could not remove file %s', $path), [
                'method' => __METHOD__
            ]);
        }
    }
}