<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Form\EpisodeType;
use App\Services\Slugify;
use Symfony\Component\Mime\Email;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, Slugify $slugify, MailerInterface $mailer): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $em->persist($episode);
            $em->flush();

            $this->addFlash("success", "Épisode bien ajouté !");

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Un nouvel épisode vient d\'être publié !')
                ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode]));

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd("not sent");
            }

            return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{episode<^[a-zA-Z0-9-]+$>}", name="episode_show", methods={"GET|POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function show(Request $request, Episode $episode, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if (!is_null($this->getUser())) {
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setAuthor($this->getUser());
                $comment->setEpisode($episode);
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('episode_show', ["episode" => $episode->getSlug()]);
            }
        }

        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{episode<^[a-zA-Z0-9-]+$>}/edit", name="episode_edit", methods={"GET", "POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function edit(
        Request $request,
        Episode $episode,
        EntityManagerInterface $em,
        Slugify $slugify
    ): Response {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $em->flush();

            $this->addFlash("success", "Épisode bien mis à jour !");

            return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{episode}/delete", name="episode_delete", methods={"POST"})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     */
    public function delete(Request $request, Episode $episode, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $episode->getId(), $request->request->get('_token'))) {
            $em->remove($episode);
            $em->flush();

            $this->addFlash("danger", "Episode bien supprimé !");
        }

        return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
