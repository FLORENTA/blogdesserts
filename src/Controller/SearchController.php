<?php

namespace App\Controller;

use App\Form\SearchModelType;
use App\Model\SearchModel;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @return Response
     */
    public function form(): Response
    {
        $form = $this->createForm(SearchModelType::class, new SearchModel(), [
            'action' => $this->generateUrl('app_search'),
        ]);

        return $this->render('search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/search",
     *     name="app_search",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param ArticleRepository $articleRepository
     */
    public function search(Request $request, ArticleRepository $articleRepository)
    {
        $searchModel = new SearchModel();
        $form = $this->createForm(SearchModelType::class, $searchModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results = $articleRepository->getSearchResults($searchModel->getValue());
            dump($results);die;
        }
    }
}