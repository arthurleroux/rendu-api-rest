<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractController
{
    /**
     * @FOSRest\Get("/api/articles")
     * @param ObjectManager $manager
     * @return Response
     */
    public function getArticlesAction(ObjectManager $manager)
    {
        $articleRepository  = $manager->getRepository(Article::class);
        $articles           = $articleRepository->findAll();

        return $this->json($articles, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/api/articles/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function getArticleAction(ObjectManager $manager, $id)
    {
        $articleRepository  = $manager->getRepository(Article::class);
        $article            = $articleRepository->find($id);

        if (!$article instanceof Article) {
            return $this->json([
                'success' => false,
                'error'   => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }
        else {
            return $this->json($article, Response::HTTP_OK);
        }
    }

    /**
     * @FOSRest\Post("/api/articles")
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @param Article $article
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postArticleAction(Article $article, ObjectManager $manager, ValidatorInterface $validator)
    {
        $errors = $validator->validate($article);

        if(!count($errors)) {
            $manager->persist($article);
            $manager->flush();

            return $this->json($article, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @FOSRest\Delete("/api/articles/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function deleteArticleAction(ObjectManager $manager, $id)
    {
        $articleRepository  = $manager->getRepository(Article::class);
        $article            = $articleRepository->find($id);

        if($article instanceof Article) {
            $manager->remove($article);
            $manager->flush();

            return $this->json([
                "success" => true
            ], Response::HTTP_OK);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @FOSRest\Put("/api/articles/{id}")
     * @param Request $request
     * @param $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateArticleAction(Request $request, ObjectManager $manager, $id, ValidatorInterface $validator)
    {
        $articleRepository  = $manager->getRepository(Article::class);
        $existingArticle    = $articleRepository->find($id);

        if(!$existingArticle instanceof Article) {
            return $this->json([
                "success" => false,
                "error" => 'Article not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ArticleType::class, $existingArticle);
        $form->submit($request->request->all());

        $errors = $validator->validate($existingArticle);

        if(!count($errors)) {
            $manager->persist($existingArticle);
            $manager->flush();

            return $this->json($existingArticle, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
