<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="livre")
     */
    public function index()
    {
        $books = $this->getDoctrine()
            ->getRepository('App\Entity\Livre')
            ->findBy([]);

        return $this->render('livre/index.html.twig', [
            'books' => $books,
        ]);
    }
}
