<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(ActorRepository $actorRepository): Response
    {
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll()
        ]);
    }


    /**
     * @Route("/{actor}", name="show", methods={"GET|POST"})
     */
    public function show(Request $request, Actor $actor, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash("success", "actor_picture.added");

            return $this->redirectToRoute('actor_show', ['actor' => $actor->getId()]);
        }

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'form' => $form->createView()
        ]);
    }
}
