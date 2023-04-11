<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\Promocode;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Entity\Product;
use App\Entity\Specification;
use Doctrine\ORM\EntityManager;
use App\Service\PromocodeService;
use App\Service\ShipmentFeesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;	
	}

    /**
     * @Route("/order", name="order")
     */
    public function index(ShipmentFeesService $shippmentFeesService,CartService $cart, Request $request): Response
    {
        if( !$this->getUser()->getAddresses()->getValues()) {

            return $this->redirectToRoute('account_address_add');
        }

        
        $orderFeesWeight = null;
        $shipmentPrice = null;

        foreach($cart->getFull() as $productInCart) {
           $orderFeesWeight = $orderFeesWeight + ($productInCart['product']['weight']*$productInCart['quantity']);
        } 

        $shipmentPrice = $shippmentFeesService->automated($orderFeesWeight);

        $form = $this->createForm(OrderType::class, null,[
            'user' => $this->getUser()
        ]); 
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'shipmentPrice' => $shipmentPrice,
            'cart' => $cart->getFull(),
        ]);
    }

    /**
     * @Route("/order/recapitulatif", name="order_recap" , methods={"POST"})
     */
    public function add(ShipmentFeesService $shippmentFeesService,CartService $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null,[
            'user' => $this->getUser()
        ]); 

        $form = $form->handleRequest($request); 

        $orderFeesWeight = null;
        $shipmentPrice = null;
        foreach($cart->getFull() as $productInCart) {
            $orderFeesWeight = $orderFeesWeight + ($productInCart['product']['weight']*$productInCart['quantity']);
        } 

        $shipmentPrice = $shippmentFeesService->automated($orderFeesWeight);

        if ($form->isSubmitted() && $form->isValid()) {
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
    
            $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
            $delivery_content .= '<br>'.$delivery->getPhone();

            
            $delivery_content .= '<br>'.$delivery->getAddress();
    

            $date = new \DateTime();

            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);

            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($shipmentPrice);
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);
            $order->setState(0);

            $this->entityManager->persist($order);
            $productPrice = 0;
            $total = 0;
            $userOrders = $this->entityManager->getRepository(Order::class)->findBy(['user'=> $this->getUser()]);

            foreach($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProductIllustration($product['product']['coverPhoto']);
                $orderDetails->setProductName($product['product']['title']);
                $orderDetails->setSellerId($product['product']['sellerId']);
                $orderDetails->setSize($product['size']);
                $orderDetails->setQuantity($product['quantity']);
                $productPrice = $product['product']['price'];

                // dd($orderDetails);
                
                $MyProduct = $this->entityManager->getRepository(Product::class)->findOneById($product['id']);
                // $sold = $productQte->setSold($product['quantity']);
                // $sold = $MyProduct['quantity'];
                // $specificationQte = 

                // $specificationQte = $this->entityManager->getRepository(Specification::class)->findOneById($specification['id']);
                // $sold = $specificationQte->setSold($product['quantity']);

                // $stock = $MyProduct - $sold;
                // $productQte->setStock($product['quantity']);
                // $specificationQte->setStock($product['quantity']);
                // dd($product['quantity']); // 10
                // dd($product);
                // dd($sold);

                $orderDetails->setPrice($productPrice);
                $orderDetails->setTotal($productPrice*$product['quantity']);
                
                
                $this->entityManager->persist($orderDetails);
                $total = $total + ($productPrice*$product['quantity']);
                
            }
                       
            $order->setTotal($total);
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'userOrders' => $userOrders,
                'totol' => $order->getTotal(),
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference(),
                'shipmentPrice' => $shipmentPrice,
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
