<?php

namespace App\Command;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class LoadCategoryCommand extends Command
{
    protected static $defaultName = 'app:load-categories';
    private $em;
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    )
    {
        $this->em = $entityManager;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    public function configure()
    {
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = Yaml::parse(file_get_contents(__DIR__.'/../../fixtures/categories.yaml'));
        $existingCategoryNames = $this->categoryRepository->getCategoryNames();
        $existingCategoryNames = array_map(function($existingCategoryName) {
            return $existingCategoryName['name'];
        }, $existingCategoryNames);
        $collection = new ArrayCollection($existingCategoryNames);
        foreach ($categories as $category) {
            if (!$collection->contains($category)) {
                $this->em->persist((new Category())->setName($category));
            }
        }
        $this->em->flush();
    }
}