<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route(
     *     "/",
     *     name="app_index",
     *     methods={"GET"}
     * )
     */
    public function index()
    {
        return $this->render("index.html.twig");
    }
}