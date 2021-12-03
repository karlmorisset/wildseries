<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/{categoryName}", name="show")
     */
    public function show(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException(
                "Aucune catégorie avec le nom {$categoryName} n'existe dans la base de données"
            );
        }

        $programsByCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()], ['id' => "DESC"], 3);


        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programsByCategory' => $programsByCategory
        ]);
    }
}
