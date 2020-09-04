<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\CommentService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(
 *     path="/articles"
 * )
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends AbstractController
{
    /**
     * @Route(
     *     name="app_articles",
     *     methods={"GET"}
     * )
     * @param LoggerInterface $logger
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function getArticles(LoggerInterface $logger, ArticleRepository $articleRepository): Response
    {
        $maxArticleResults = $this->getParameter('app.max_article_results');
        /** @var Article[] */
        $articles = $articleRepository->findArticles($maxArticleResults);

        try {
            $numberOfArticles = $articleRepository->getNumberOfArticles();
        } catch (NoResultException|NonUniqueResultException $e) {
            $logger->error($e->getMessage(), ['method' => __METHOD__]);
            $numberOfArticles = null;
        }

        return $this->render('article/articles.html.twig', [
            'articles' => $articles,
            'display_more_articles_button' => $numberOfArticles ? $numberOfArticles > $maxArticleResults : false,
        ]);
    }

    /**
     * @Route(
     *     "/cards",
     *     name="app_articles_cards",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @param TranslatorInterface $translator
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @return JsonResponse
     */
    public function cards(
        TranslatorInterface $translator,
        Request $request,
        ArticleRepository $articleRepository
    ): JsonResponse
    {
        $lastId = $request->get('lastId');
        $maxArticleResults = $this->getParameter('app.max_article_results');
        /** @var Article[] */
        $articles = $articleRepository->findArticles($maxArticleResults, $lastId);
        if (empty($articles)) {
            return new JsonResponse($translator->trans('article.show_more.failure'), JsonResponse::HTTP_BAD_REQUEST);
        }
        $data['cards'] = $this->renderView('article/includes/_cards.html.twig', ['articles' => $articles,]);
        $data['last_article_id'] = count($articles) ? end($articles)->getId() : null;

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="app_article",
     *     methods={"GET"}
     * )
     * @param CommentService $commentService
     * @param Article $article
     * @return Response
     */
    public function getArticle(Article $article, CommentService $commentService): Response
    {
        return $this->render('article/article.html.twig', [
            'article' => $article,
            'nb_visible_comments' =>  $commentService->getNumberOfVisibleCommentsWithNoParentForObject($article),
        ]);
    }
}