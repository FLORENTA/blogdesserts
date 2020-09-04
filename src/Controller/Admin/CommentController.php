<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(
 *     path="/admin/comments"
 * )
 * Class CommentController
 * @package App\Controller\Admin
 */
class CommentController extends AbstractController
{
    /**
     * @Route(
     *     name="app_admin_comment_index",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('comment/comments.html.twig');
    }

    /**
     * @Route(
     *     name="app_admin_comment_list",
     *     methods={"GET"},
     * )
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function list(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/includes/_list.html.twig', [
            'comments' => $commentRepository->getList(),
        ]);
    }

    /**
     *
     * @Route(
     *     "/{id}",
     *     name="app_admin_delete_comment",
     *     methods={"DELETE"}
     * )
     * @param TranslatorInterface $translator
     * @param Comment $comment
     * @return JsonResponse
     */
    public function delete(TranslatorInterface $translator, Comment $comment): JsonResponse
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($comment);
        $manager->flush();

        return new JsonResponse($translator->trans('comment.deletion.success'), JsonResponse::HTTP_OK);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="app_admin_update_comment",
     *     methods={"PUT"}
     * )
     * @param Request $request
     * @param Comment $comment
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $onlyVisibleField = $request->query->getBoolean('onlyVisibleField', false);
        $manager = $this->getDoctrine()->getManager();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_admin_update_comment', [
                'id' => $comment->getId(),
                'onlyVisibleField' => $onlyVisibleField,
            ]),
            'method' => Request::METHOD_PUT,
            'edit' => true,
            'only_visible_field' => $onlyVisibleField,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->render('comment/comment_form.html.twig', [
            'form' => $form->createView(),
            'only_visible_field' => $onlyVisibleField,
        ]);
    }
}