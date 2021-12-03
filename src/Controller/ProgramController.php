<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Program;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
     * Récupère une série par son id
     *
     * @Route("/{id<^[0-9]+$>}", methods={"GET"}, name="show")
     * @return Response
     */
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['id' => $id]);

        $seasons = $program->getSeasons();

        if (!$program) {
            throw $this->createNotFoundException(
                "Aucune série avec l'identifiant {$id} n'existe dans la base de données"
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
     * @Route("/{programId<^[0-9]+$>}/season/{seasonId<^[0-9]+$>}", methods={"GET"}, name="season_show")
     * @return Response
     */
    public function showSeason(int $programId, int $seasonId): Response
    {
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['id' => $programId]);
        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['id' => $seasonId]);
        $episodes = $season->getEpisodes();

        if (!$program) {
            throw $this->createNotFoundException(
                "Aucune série avec l'identifiant {$programId} n'existe dans la base de données"
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                "Aucune saison avec l'identifiant {$seasonId} n'existe dans la base de données"
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }
}
