<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * The controller for the category add form
     *
     * @Route("/new", name="new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request) : Response
    {
        // Create a new Category Object
        $category = new Category();

        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);

        // Get data from HTTP request
        $form->handleRequest($request);

        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash("success", "Catégorie bien ajoutée !");

            return $this->redirectToRoute('category_index');
        }

        // Render the form
        return $this->render('category/new.html.twig', ["form" => $form->createView()]);
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
