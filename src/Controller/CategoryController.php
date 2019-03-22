<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    /**
     * @FOSRest\Get("/api/categories")
     * @param ObjectManager $manager
     * @return Response
     */
    public function getCategoriesAction(ObjectManager $manager)
    {
        $categoryRepository  = $manager->getRepository(Category::class);
        $categories          = $categoryRepository->findAll();

        return $this->json($categories, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("/api/categories/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function getCategoryAction(ObjectManager $manager, $id)
    {
        $categoryRepository  = $manager->getRepository(Category::class);
        $category            = $categoryRepository->find($id);

        if (!$category instanceof category) {
            return $this->json([
                'success' => false,
                'error'   => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }
        else {
            return $this->json($category, Response::HTTP_OK);
        }
    }

    /**
     * @FOSRest\Post("/api/categories")
     * @ParamConverter("category", converter="fos_rest.request_body")
     * @param Category $category
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function postcategoryAction(category $category, ObjectManager $manager, ValidatorInterface $validator)
    {
        $errors = $validator->validate($category);

        if(!count($errors)) {
            $manager->persist($category);
            $manager->flush();

            return $this->json($category, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @FOSRest\Delete("/api/categories/{id}")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function deleteCategoryAction(ObjectManager $manager, $id)
    {
        $categoryRepository  = $manager->getRepository(Category::class);
        $category            = $categoryRepository->find($id);

        if($category instanceof Category) {
            $manager->remove($category);
            $manager->flush();

            return $this->json([
                "success" => true
            ], Response::HTTP_OK);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @FOSRest\Put("/api/categories/{id}")
     * @param Request $request
     * @param $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCategoryAction(Request $request, ObjectManager $manager, $id, ValidatorInterface $validator)
    {
        $categoryRepository  = $manager->getRepository(Category::class);
        $existingCategory    = $categoryRepository->find($id);

        if(!$existingCategory instanceof Category) {
            return $this->json([
                "success" => false,
                "error" => 'category not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CategoryType::class, $existingCategory);
        $form->submit($request->request->all());

        $errors = $validator->validate($existingCategory);

        if(!count($errors)) {
            $manager->persist($existingCategory);
            $manager->flush();

            return $this->json($existingCategory, Response::HTTP_CREATED);
        }
        else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage(). ' (' . $errors[0]->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
