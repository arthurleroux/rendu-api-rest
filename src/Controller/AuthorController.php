<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    /**
     * @FOSRest\Get("/api/authors")
     * @param ObjectManager $manager
     * @return Response
     */
    public function getAuthorsAction(ObjectManager $manager)
    {
        $authorRepository  = $manager->getRepository(Author::class);
        $authors           = $authorRepository->findAll();

        return $this->json($authors, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/api/authors/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function getAuthorAction(ObjectManager $manager, $id)
    {
        $authorRepository  = $manager->getRepository(Author::class);
        $author            = $authorRepository->find($id);

        if (!$author instanceof Author) {
            return $this->json([
                'success' => false,
                'error'   => 'Author not found'
            ], Response::HTTP_NOT_FOUND);
        }
        else {
            return $this->json($author, Response::HTTP_OK);
        }
    }

    /**
     * @FOSRest\Post("/api/authors")
     * @ParamConverter("author", converter="fos_rest.request_body")
     * @param Author $author
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postAuthorAction(Author $author, ObjectManager $manager, ValidatorInterface $validator)
    {
        $errors = $validator->validate($author);

        if(!count($errors)) {
            $manager->persist($author);
            $manager->flush();

            return $this->json($author, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @FOSRest\Delete("/api/authors/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function deleteAuthorAction(ObjectManager $manager, $id)
    {
        $authorRepository  = $manager->getRepository(Author::class);
        $author            = $authorRepository->find($id);

        if($author instanceof Author) {
            $manager->remove($author);
            $manager->flush();

            return $this->json([
                "success" => true
            ], Response::HTTP_OK);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => 'Author not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @FOSRest\Put("/api/authors/{id}")
     * @param Request $request
     * @param $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAuthorAction(Request $request, ObjectManager $manager, $id, ValidatorInterface $validator)
    {
        $authorRepository  = $manager->getRepository(Author::class);
        $existingauthor    = $authorRepository->find($id);

        if(!$existingauthor instanceof Author) {
            return $this->json([
                "success" => false,
                "error" => 'Author not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(AuthorType::class, $existingauthor);
        $form->submit($request->request->all());

        $errors = $validator->validate($existingauthor);

        if(!count($errors)) {
            $manager->persist($existingauthor);
            $manager->flush();

            return $this->json($existingauthor, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
