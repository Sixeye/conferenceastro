<?php

namespace App\service\Cart;

use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{

    protected $session;
    protected $bookRepository;

    public function __construct(SessionInterface $session, LivreRepository $bookRepository)
    {
        $this->session = $session;
        $this->bookRepository= $bookRepository;
    }

    public function add(int $id){

        $panier=$this->session->get('panier', []);
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
    
        $this->session->set('panier', $panier);

    }

    public function remove(int $id){
        $panier =$this->session->get('panier', []);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);

    }

    public function getFullCart() : array {

        $panier = $this->session->get('panier', []);

        $panierWithData =[];

        foreach($panier as $id=>$quantity){
            $panierWithData[] = [
                'book' => $this->bookRepository->find($id),
                'quantity' => $quantity
            ];    
        }

        return $panierWithData;
    }

    public function getTotal() : float {

        $total = 0;

        foreach($this->getFullCart() as $item){
        
            $total+=$item['book']->getPrixht() * $item['quantity'];

        }
        return $total;
    }

}