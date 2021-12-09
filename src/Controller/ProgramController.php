<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Services\Slugify;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * Liste toutes les séries
     *
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * Créé une série
     *
     * @Route("/new", methods={"GET", "POST"}, name="new")
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $em, Slugify $slugify, MailerInterface $mailer)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $program->setSlug($slugify->generate($program->getTitle()));
            $em->persist($program);
            $em->flush();


            $email = (new Email())
                ->from("ok@ok.com")
                ->to('karl.morisset@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html('<p>Une nouvelle série vient d\'être publiée sur Wild Séries !</p>');

            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }


    /**
     * Récupère une série par son id
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}", methods={"GET"}, name="show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @return Response
     */
    public function show(Program $program): Response
    {
        $seasons = $program->getSeasons();

        if (!$program) {
            throw $this->createNotFoundException(
                "Aucune série avec l'identifiant {$program->id} n'existe dans la base de données"
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }


    /**
     * Récupère une série par son id
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}/season/{season<^[0-9]+$>}", methods={"GET"}, name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @return Response
     */
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();

        if (!$program) {
            throw $this->createNotFoundException(
                "Aucune série avec l'identifiant {$program->id} n'existe dans la base de données"
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                "Aucune saison avec l'identifiant {$season->id} n'existe dans la base de données"
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }

    /**
     * Récupère une série par son slug
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}/season/{season<^[0-9]+$>}/episode/{episode<^[a-zA-Z0-9-]+$>}", methods={"GET"}, name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode": "slug"}})
     * @return Response
     */
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}
