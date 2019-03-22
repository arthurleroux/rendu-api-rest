<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;

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
    
}
