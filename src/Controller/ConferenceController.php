<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentaireRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }

    /**
     * @Route("/conference/{slug}", name="conference")
     */
    public function show(Request $request, Conference $conference, CommentaireRepository $commentaireRepository)
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentaireRepository->getCommentairePaginator($conference, $offset);
        return new Response($this->twig->render('conference/show.html.twig', [
            'conference'   => $conference,
            'commentaires' => $paginator,
            'previous'     => $offset - CommentaireRepository::PAGINATOR_PER_PAGE,
            'next'         => min(count($paginator), $offset + CommentaireRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
