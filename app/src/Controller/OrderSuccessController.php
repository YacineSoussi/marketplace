<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Service\CartService;
use App\Service\NotifierService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Notifier\Notifier;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;	
	}

    /**
     * @Route("/test")
     */
    public function tst() {

       
        $queryBuilder = $this->entityManager->createQueryBuilder('o');
        $queryBuilder->select('o')
                    ->from(OrderDetails::class, 'o')
                    ->leftJoin('o.myOrder','m')
                    ->where('m.isPaid = true')
                    ->andWhere('o.sellerId = :seller')
                    ->setParameter('seller', $seller) 
                   
        ;

        $query = $queryBuilder->getQuery();
        $orderDetails = $query->getResult();


    }

    /**
     * @Route("/order/payment-success/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, CartService $cart, MailerInterface $mailer, NotifierService $notifier): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
      
        if(!$order || $order->getUser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0){
           
            $cart->remove();
            $order->setIsPaid(1);
            $order->setState(1);
           
            
            $this->entityManager->flush();
            $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@moon-factory.fr', 'Marketplace'))
                    ->to($order->getUser()->getEmail())
                    ->subject('Commande validée')
                    ->htmlTemplate('mail/order_success.html.twig')
                    ->context([
                        'order' => $order,
                    ]);
           
            $mailer->send($email);
            
        }
        foreach($order->getOrderDetails() as $details ) {
            $content = "Félicitation votre article ".$details->getProductName()."a été vendu";
            $notifier->notifySeller($details->getSellerId(),$content);
        }
        
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }


}
