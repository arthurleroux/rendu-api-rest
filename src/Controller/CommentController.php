<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{
    /**
     * @FOSRest\Get("/api/comments")
     * @param ObjectManager $manager
     * @return Response
     */
    public function getCommentsAction(ObjectManager $manager)
    {
        $commentRepository  = $manager->getRepository(Comment::class);
        $comments           = $commentRepository->findAll();

        return $this->json($comments, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/api/comments/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function getCommentAction(ObjectManager $manager, $id)
    {
        $commentRepository  = $manager->getRepository(Comment::class);
        $comment            = $commentRepository->find($id);

        if (!$comment instanceof Comment) {
            return $this->json([
                'success' => false,
                'error'   => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }
        else {
            return $this->json($comment, Response::HTTP_OK);
        }
    }

    /**
     * @FOSRest\Post("/api/comments")
     * @ParamConverter("comment", converter="fos_rest.request_body")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postCommentAction(Comment $comment, ObjectManager $manager, ValidatorInterface $validator)
    {
        $errors = $validator->validate($comment);

        if(!count($errors)) {
            $manager->persist($comment);
            $manager->flush();

            return $this->json($comment, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @FOSRest\Delete("/api/comments/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function deleteCommentAction(ObjectManager $manager, $id)
    {
        $commentRepository  = $manager->getRepository(Comment::class);
        $comment            = $commentRepository->find($id);

        if($comment instanceof Comment) {
            $manager->remove($comment);
            $manager->flush();

            return $this->json([
                "success" => true
            ], Response::HTTP_OK);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @FOSRest\Put("/api/comments/{id}")
     * @param Request $request
     * @param $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCommentAction(Request $request, ObjectManager $manager, $id, ValidatorInterface $validator)
    {
        $commentRepository  = $manager->getRepository(Comment::class);
        $existingComment    = $commentRepository->find($id);

        if(!$existingComment instanceof Comment) {
            return $this->json([
                "success" => false,
                "error" => 'Comment not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CommentType::class, $existingComment);
        $form->submit($request->request->all());

        $errors = $validator->validate($existingComment);

        if(!count($errors)) {
            $manager->persist($existingComment);
            $manager->flush();

            return $this->json($existingComment, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
