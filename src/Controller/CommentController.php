<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentContext;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTime;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(
 *     path="/comments"
 * )
 * Class CommentController
 * @package App\Controller
 */
class CommentController extends AbstractController
{
    /**
     * @Route(
     *     "/form",
     *     name="app_comment_form",
     *     methods={"GET"}
     * )
     * @throws InvalidArgumentException
     * @param object|null $object
     * @param int|null $parentId
     * @return Response
     */
    public function form($object = null, int $parentId = null): Response
    {
        if (!\is_object($object)) {
           throw new InvalidArgumentException('Missing object');
        }
        $comment = (new Comment());
        $commentContext = (new CommentContext())
            ->setComment($comment)
            ->setClass(get_class($object));

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_new_comment', [
                'objectId' => $object->getId(),
                'parentId' => $parentId,
            ]),
            'comment_context' => $commentContext,
        ]);

        return $this->render('comment/comment_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     name="app_new_comment",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param CommentRepository $commentRepository
     * @return Response|RedirectResponse
     */
    public function new(
        Request $request,
        TranslatorInterface $translator,
        CommentRepository $commentRepository
    ): Response
    {
        $objectId = $request->get('objectId');
        $parentId = $request->get('parentId');
        $commentForm = $request->get('comment');
        $class = $commentForm['class'] ?? null;

        if (null === $commentForm || null === $objectId || null === $class) {
            $request->getSession()->getFlashBag()->add('error', $translator->trans('comment.submission.failure'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $comment = (new Comment());
        $commentContext = (new CommentContext())
            ->setObjectId($objectId)
            ->setClass($class)
            ->setComment($comment);

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_new_comment', [
                'objectId' => $objectId,
                'parentId' => $parentId,
            ]),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $parentId) {
                /** @var Comment|null */
                $parent = $commentRepository->find($parentId);
                if (!$parent) {
                    throw $this->createNotFoundException('Unknown parent comment');
                }
                $parent->setChild($comment);
            }
            $comment->setCreatedAt(new DateTime());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->persist($commentContext);
            $manager->flush();

            $request->getSession()->getFlashBag()->add('success', $translator->trans('comment.submission.success'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->render('comment/comment_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/",
     *     name="app_object_comment_list",
     *     methods={"GET"},
     * )
     * @param LoggerInterface $logger
     * @param CommentRepository $commentRepository
     * @param object|null $object
     * @return Response
     */
    public function show(LoggerInterface $logger, CommentRepository $commentRepository, $object = null): Response
    {
        try {
            /** @var Comment[] */
            $comments = $commentRepository->findAllForObject($object);
        } catch (InvalidArgumentException $exception) {
            $logger->error($exception->getMessage(), ['method' => __METHOD__]);
            $comments = [];
        }

        return $this->render('comment/includes/_object_list.html.twig', [
            'comments' => $comments,
        ]);
    }
}