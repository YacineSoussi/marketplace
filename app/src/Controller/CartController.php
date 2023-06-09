<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(CartService $cart)
    {
           
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getFull(),
                    
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add(CartService $cart, $id)
    { 
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function remove(CartService $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     */
    public function delete(CartService $cart, $id)
    {
        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }

     /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     */
    public function decrease(CartService $cart, $id)
    {
        $cart->decrease($id);
        return $this->redirectToRoute('cart');
    }
}
