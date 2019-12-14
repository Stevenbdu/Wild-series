<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Form\CommentType;
use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProgramSearchType;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


Class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {

        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }


        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/showProgram/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_showProgram")
     * @return Response
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }
        $seasons = $program->getSeason();

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @param string $categoryName
     * @Route("wild/category/{categoryName}", name="show_category").
     */
    public function showByCategory(string $categoryName)
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category name has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
                ['id' => 'DESC'], 3
            );
        if (!$programs) {
            throw $this
                ->createNotFoundException('No program name has been sent to find a program in program\'s table.');
        }
        return $this->render('wild/category.html.twig', [
            'category' => $categoryName,
            'programs' => $programs,
        ]);
    }

    /**
     * @param integer $id
     * @Route("wild/showSeason/{id}", name="show_season").
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No id has been sent.');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'episodes' => $episodes,
            'program' => $program,
        ]);
    }

    /**
     * @param integer $id
     * @Route("wild/showEpisode/{id}", name="show_episode").
     */
    public function showEpisode(Episode $episode, Request $request)
    {
        $comment = new Comment();

        $season = $episode->getSeason();
        $program = $season->getProgram();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $comment->setRate($data->getRate());
            $comment->setComment($data->getComment());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show_episode', ['id' => $episode->getId()]);
        }

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param integer $id
     * @Route("wild/actor/{id}", name="show_actor").
     */
    public function showActor(Actor $actor)
    {
        $programs = $actor->getPrograms();
        return $this->render('wild/actor.html.twig', [
            'programs' => $programs,
            'actor' => $actor,
        ]);
    }

}
