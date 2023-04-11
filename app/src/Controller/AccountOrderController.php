<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Order;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountOrderController extends AbstractController
{
    public const TRACK_ETAPE_1 = [
        'DR1'
    ];

    public const TRACK_ETAPE_2 = [
        'PC1','PC2',
    ];

    public const TRACK_ETAPE_3 = [
        'PC1','PC2','ET1','ET2','ET3','ET4','MD2','ND1','AG1'
    ];

    public const TRACK_ETAPE_4 = [
        'DI1','DI2'
    ];
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/my-orders", name="account_order")
     */
    public function index(Request $request,PaginatorInterface $paginator)
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        $ordersPaginate = $paginator->paginate(
            // Doctrine Query, not results
            $orders,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
        );

        
        return $this->render('account/order.html.twig', [
            'ordersPaginate' => $ordersPaginate,
             

        ]);
    }

    /**
     * @Route("/account/my-orders/{reference}", name="account_order_show")
     */
    public function show($reference)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        $state= array();
        
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }

        if ($order->getTrackNumber())
        {
            $client = HttpClient::create();
        
            $response = $client->request('GET','https://api.laposte.fr/suivi/v2/idships/'.$order->getTrackNumber(),[
                'headers' => [
                    'Accept' => 'application/json',
                    // 'X-Okapi-Key' => 'bORhegOEEjVkOSKwsdV8HSmBhJzShceIX/d7ewRozXgf9NRIZuGFDNwvpUavc4h2' // clé de production
                    'X-Okapi-Key' =>'XRIhlti+P6ou7GrMryy1eN0XhGPNEeEQd3b6k8AZVwN+T5UTZxizmhPT9wbrJ171'   
                ]
            ]);
                    
        
            $content = json_decode($response->getContent(), true);
            $content = $response->toArray();
            $count= 0;
            
            $tabEtape1 = ['DR1'];
            $tabEtape2 = ['PC1','PC2'] ;
            $tabEtape3 = ['PC1','PC2','ET1','ET2','ET3','ET4','MD2','ND1','AG1'] ;
            $tabEtape4 = ['DI1','DI2']; 
            foreach($content['shipment']['event'] as $event) {
                $count++;
                array_push($state,$event);
            }
            
            $state = $state[0];
            if (in_array($state['code'],$tabEtape1) ) {
                
                $order->setState(1);
                $this->entityManager->flush();
            }
            elseif (in_array($state['code'],$tabEtape2) ) {
                
                $order->setState(2);
                $this->entityManager->flush();
            }
            elseif (in_array($state['code'],$tabEtape3) ) {
                
                $order->setState(3);
                $this->entityManager->flush();
            }
            elseif (in_array($state['code'],$tabEtape4) ) {
                
                $order->setState(4);
                $this->entityManager->flush();
            }
        }
        
        
        return $this->render('account/order_show.html.twig', [
            'order' => $order,
            'tableauevent' => $state,

        ]);
       
    }
}