<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private $entityManager;
 

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session) {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
	{
		$cart = $this->session->get('cart',[]);
		if(!empty($cart[$id])){
			$cart[$id]++;
		}else{
			$cart[$id] = 1;
		}

		return $this->session->set('cart',$cart);
		
	}
	public function get() 
	{
		return $this->session->get("cart");
	}
	public function remove() 
	{
		return $this->session->remove('cart');
	}
	public function delete($id) 
	{
		$cart = $this->session->get('cart',[]);
		unset($cart[$id]);

		return $this->session->set('cart',$cart);
	}
	public function decrease($id)
	{
		$cart = $this->session->get('cart',[]);

		if($cart[$id] > 1){
			$cart[$id]--;
		}else{
			unset($cart[$id]);
		}

		return $this->session->set('cart',$cart);
	}

    public function getFull() {
      
        $cartComplete = [];
        $attributSession = $this->session->get('attributSession');
        if($this->get())
        {   
            foreach( $this->get() as $id => $quantity){    
				$cartComplete[] = [
					'id'=> $id, 
                    'product' => $attributSession[$id]['product'],
					'size' => $attributSession[$id]['size'],
                    'quantity' => $quantity
                ];
                
            }                
        }
        return $cartComplete;
    }

}