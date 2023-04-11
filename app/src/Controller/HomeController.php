<?php

namespace App\Controller;

use App\Entity\Notifier;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;
	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;	
	}
    
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $ProductRepository): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findBy([
            'isBest' => true
        ]);
        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
        ]);
    }

     /**
     * @Route("/api/notification/{id}", name="updateNotify",methods={"PUT","GET"})
     */
    public function updateOrderWithPromo($id): Response
    {
        $notifier = $this->entityManager->getRepository(Notifier::class)->findOneById($id);
       
        $notifier->setIsViewed(true);
        $this->entityManager->persist($notifier);
        $this->entityManager->flush();
        dd('done');
    }
}
