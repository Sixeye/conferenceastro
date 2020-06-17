<?php

namespace App\Controller;


use App\service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     * @param CartService $cartService
     * @return Response
     */
    public function index(CartService $cartService)
    {
    
        return $this->render('panier/index.html.twig', [
            'items'=> $cartService->getFullCart(),
            'total'=>$cartService->getTotal()
        ]);
    }

    /**
     *
     * @Route("/panier/add/{id}", name="cart_add")
     * @param $id
     * @param CartService $cartService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function add($id, CartService $cartService) 
    {
        
        $cartService->add($id);
        return $this->redirectToRoute("cart_index");
    }

/**
 * 
 * @Route("/panier/remove/{id}", name="cart_remove")
 * 
 */

    public function remove($id,  CartService $cartService){
        
        $cartService -> remove($id);

        return $this->redirectToRoute("cart_index");
    }
}
