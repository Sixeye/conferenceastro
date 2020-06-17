<?php
namespace App\Controller;

use App\Repository\LivreRepository;
use Knp\Component\Pager\PaginatorInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/checkout")
     * @param Request $request
     * @return Response
     * @throws ApiErrorException
     */
    public function checkout(Request $request)
    {

//        Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);
//
//        $sessionData = [
//            'payment_method_types' => ['card'],
//            'line_items' => [],
//            'success_url' => 'http://localhost:8000/success?session_id={CHECKOUT_SESSION_ID}',
//            'cancel_url' => 'http://localhost:8000/basket',
//        ];


        Stripe::setApiKey($_ENV['STRIPE_PRIVATE_KEY']);

    $sessionData = [
        'payment_method_types' => ['card'],
        'line_items' => [[
        'name' => 'total',
        'amount' => ($_POST['total'])*100,
        'currency' => 'eur',
        'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:8000/success?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost:8000/panier',
    ];

//        foreach ($this->session->get('panier') as $book) {
//            $sessionData['line_items'][] = [
//                'name' => $book['titre'],
//                'description' => $book['auteur'],
//                'amount' => ($book['prixht'] * 100),
//                'currency' => 'eur',
//                'quantity' => 1
//            ];
//        }


        $session = Session::create($sessionData);
        
        return $this->render('panier/checkout.html.twig', [

            'sessionId' => $session['id']
        
        ]);
    
    }

    /**
     * @Route("/vider", name="vider")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param LivreRepository $bookRepository
     * @return Response
     */

    public function emptyCart(PaginatorInterface $paginator, Request $request, LivreRepository $bookRepository){
        $session = session_destroy();
        return $this->render('livre/index.html.twig', [
            // 'books' => "tous les books"
            'books' => $paginator->paginate($bookRepository->findAll(),$request->query->getInt('page', 1), 6)
        ]);

    }

}

