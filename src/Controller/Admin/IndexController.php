<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/admin"
 * )
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route(
     *     name="app_admin_index",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function adminIndex(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}