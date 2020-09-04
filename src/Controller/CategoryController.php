<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/categories"
 * )
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{
    /**
     * @Route(
     *     "/{id}/articles",
     *     name="app_categories_for_article",
     *     methods={"GET"}
     * )
     * @param Category $category
     */
    public function getArticlesForCategory(Category $category)
    {
        return $this->render('category/articles.html.twig', [
            'category' => $category
        ]);
    }
}