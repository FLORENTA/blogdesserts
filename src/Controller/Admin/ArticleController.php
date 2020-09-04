<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/admin/articles"
 * )
 * Class ArticleController
 * @package App\Controller\Admin
 */
class ArticleController extends AbstractController
{
    /**
     * @Route(
     *     name="app_admin_articles",
     *     methods={"GET"}
     * )
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function gets(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();

        return $this->render('article/list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route(
     *     "/new",
     *     name="app_admin_new_article",
     *     methods={"GET", "POST"}
     * )
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('article/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/edit",
     *     name="app_admin_update_article",
     *     methods={"GET", "PUT"}
     * )
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function update(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'method' => Request::METHOD_PUT,
            'edit' => true
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->render('article/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="app_admin_delete_article",
     *     methods={"DELETE"}
     * )
     * @param Article $article
     * @return RedirectResponse
     */
    public function delete(Article $article): RedirectResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute('app_admin_articles');
    }
}