<?php

namespace App\Controller;


use App\Entity\Commande;
use App\Form\CommandeType;
use App\service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     * @param CartService $cartService
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function index(CartService $cartService, EntityManagerInterface $manager, Request $request)
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $user = $this->getUser();

        if ($request->isMethod('POST')){

            $commande->setUser($user);
            $manager->flush();
        }

        return $this->render('panier/index.html.twig', [
            'items'=> $cartService->getFullCart(),
            'total'=>$cartService->getTotal(),
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route("/panier/add/{id}", name="cart_add")
     * @param $id
     * @param CartService $cartService
     * @return RedirectResponse
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
