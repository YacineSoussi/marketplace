<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderDetails;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, CartService $cart, $reference): Response
    {
        $YOUR_DOMAIN = 'http://localhost:8005';
        $products_for_srtipe =[];

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        

        if($order){
        $response = new JsonResponse(['error' => 'order']);
        }

        foreach($order->getOrderDetails()->getValues() as $orderDetails){
         
          
          $products_for_stripe[] = [
              'price_data' => [
                'currency' => 'eur',
                'unit_amount' => ceil($orderDetails->getPrice()*100),
                'product_data' => [
                  'name' => $orderDetails->getProductName(),
                  
                ],
              ],
              'quantity' => $orderDetails->getQuantity(),
              // dd($orderDetails),
          ];
        
        }

        $products_for_stripe[] = [
            'price_data' => [
              'currency' => 'eur',
              'unit_amount' => ceil($order->getCarrierPrice()*100),
              'product_data' => [
                'name' => $order->getCarrierName(),
              ],
            ],
            'quantity' => 1
        ];
    
        Stripe::setApiKey('sk_test_51KgszjIllJ3lgbPzBaREP7Aw7LCfuJwmQrRF4U8lVz9ZR6Dr5bUKpQmwdzJgvjLdlHN4FisRPfkvghzL0JKCBITe009G2qIX8g');
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN.'/order/payment-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN.'/order/payment-fail/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response;
        
    }
}
