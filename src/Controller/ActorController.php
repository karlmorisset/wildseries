<?php

namespace App\Controller;

use App\Entity\Actor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/actor", name="actor_")
 */
class ActorController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()->getRepository(Actor::class)->findAll();

        return $this->render('actor/index.html.twig', [
            'actors' => $actors,
        ]);
    }


    /**
     * @Route("/{actor}", name="show")
     */
    public function show(Actor $actor): Response
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }
}
