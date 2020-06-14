<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="livre")
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {



        $datas = $this->getDoctrine()
            ->getRepository('App\Entity\Livre')
            ->findBy([]);

        $books = $paginator->paginate(
          $datas,
          $request->query->getInt('page', 1),
          4
        );

        return $this->render('livre/index.html.twig', [
            'books' => $books,
        ]);
    }
}
