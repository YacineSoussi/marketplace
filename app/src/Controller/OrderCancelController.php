<?php

namespace App\Controller;
use App\Entity\Order;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;


class OrderCancelController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;	
    }
    /**
     * @Route("/order/payment-fail/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
