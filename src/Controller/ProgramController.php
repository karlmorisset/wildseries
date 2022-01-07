<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Form\SearchProgramFormType;
use App\Repository\ProgramRepository;
use App\Services\Slugify;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        $programs = $programRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()["search"];

            if(!empty($search)) $programs = $programRepository->findLikeNameOrActor($search);
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView()
        ]);
    }

    /**
     * Créé une série
     *
     * @Route("/new", methods={"GET|POST", "POST"}, name="new")
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $em, Slugify $slugify, MailerInterface $mailer)
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $program->setSlug($slugify->generate($program->getTitle()));
            $program->setOwner($this->getUser());

            $em->persist($program);
            $em->flush();

            $this->addFlash("success", "program.added");

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd("not sent");
            }

            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }


    /**
     * Récupère une série par son id
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}", methods={"GET|POST"}, name="show")
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
     * Mise à jour d'une série
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}/edit", methods={"GET|POST"}, name="edit")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
     * @return Response
     */
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() != $program->getOwner()) {
            throw new AccessDeniedException("Seul le créateur de la série peut la modifier");
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash("success", "program.edited");

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }


    /**
     * Récupère une saison par son id et par sa série
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}/season/{season<^[0-9]+$>}", methods={"GET|POST"}, name="season_show")
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
     * Récupère un épisode par son slug, l'id de la saison et le slug de la série
     *
     * @Route("/{program<^[a-zA-Z0-9-]+$>}/season/{season<^[0-9]+$>}/episode/{episode<^[a-zA-Z0-9-]+$>}", methods={"GET|POST"}, name="episode_show")
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

    /**
     * Ajoute une série à la watchlist de l'utilisateur connecté
     *
     * @Route("/{id}/watchlist", name="add_to_watchlist", methods={"GET", "POST"})
     * @return Response
     */
    public function addToWatchlist(Program $program, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->isInWatchlist($program)) {
            $this->getUser()->removeFromWatchlist($program);
        }else{
            $this->getUser()->addToWatchlist($program);
        }

        $em->flush();

        return $this->json([
            'isInWatchlist' => $this->getUser()->isInWatchlist($program)
        ]);
    }
}
