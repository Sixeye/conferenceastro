<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Conference;
use App\Form\CommentaireFormType;
use App\Repository\CommentaireRepository;
use App\Repository\ConferenceRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
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
    public function show(Request $request, Conference $conference, CommentaireRepository $commentaireRepository, SpamChecker $spamChecker, string $photoDir)
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireFormType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $commentaire->setConference($conference);
            if ($photo = $form['photo']->getData()){
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e){
                    // L'upload de la photo s'est mal passé
                    // TODO Flashbag avec un message indiquant l'erreur
                }
                $commentaire->setFilename($filename);
                $commentaire->setCreatedAt(new \DateTime('now'));
            }

            $this->entityManager->persist($commentaire);

            $context = [
                'user_ip'    => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer'   => $request->headers->get('referer'),
                'permalink'  => $request->getUri(),
            ];
            if (2 === $spamChecker->getSpamScore($commentaire, $context)){
                throw new \RuntimeException('No spams here!');
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentaireRepository->getCommentairePaginator($conference, $offset);
        return new Response($this->twig->render('conference/show.html.twig', [
            'conference'       => $conference,
            'commentaires'     => $paginator,
            'previous'         => $offset - CommentaireRepository::PAGINATOR_PER_PAGE,
            'next'             => min(count($paginator), $offset + CommentaireRepository::PAGINATOR_PER_PAGE),
            'commentaire_form' => $form->createView(),
        ]));
    }
}

//TODO faire le système d'authenfication avec mdp et aussi définir les rôles