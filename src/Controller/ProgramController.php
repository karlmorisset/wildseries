<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/program", name="program_")
*/
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Séries',
         ]);
    }

    /**
     * @Route("/{id<\d+>}", methods={"GET"}, name="show")
     */
    public function show(int $id): Response
    {
        return $this->render('program/show.html.twig', ['id' => $id]);
    }


    /**
    * Correspond à la route /program/new et au name "program_new"
    * @Route("/new", name="new")
    */
    public function new(): Response
    {
        return $this->redirectToRoute('program_show', ['id' => 4]);
    }
}
