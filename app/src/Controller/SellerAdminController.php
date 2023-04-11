<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Seller;
use App\Entity\Product;
use App\Form\SellerType;
use App\Entity\OrderDetails;
use App\Form\UpdateOrderType;
use App\Form\AdminCreateSellerType;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class SellerAdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/seller/create", name="admin_seller_create")
     */
    public function create(Request $request): Response
    {
        $seller = new Seller();
        $form = $this->createForm(AdminCreateSellerType::class, $seller);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData();
            $seller->setFirstnameCEO($user->getFirstname());
            $seller->setLastnameCEO($user->getLastname());
            $seller->setContactMail($user->getEmail());

            $this->entityManager->persist($seller);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('seller/admin_create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/sellers/", name="app_seller_index")
     */
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/seller/{id}", name="app_seller_show", methods={"GET"})
     */
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

     /**
     * @Route("/admin/seller/delete/{id}", name="app_seller_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->request->get('_token'))) {
            $sellerRepository->remove($seller);
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/admin/seller/{id}/edit", name="app_seller_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        $form = $this->createForm(AdminCreateSellerType::class, $seller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sellerRepository->add($seller);
            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    

     /**
     * @Route("/shop/dashboard", name="app_seller_dashboard")
     */
    public function indeeedex(): Response
    {
      
        return $this->render('seller_space/index.html.twig', [
          
        ]);
    }

    /**
     * @Route("/shop/products", name="seller_products")
    */
    public function sellersProducts() {
       
        $user = $this->getUser()->getSeller();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }
        $products = $this->entityManager->getRepository(Product::class)->findBy(
            [
                "seller" => $user
            ]
        );
        
        return $this->render('admin/admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/shop/orders", name="seller_orders", methods={"GET"})
    */
    public function sellersOrders(PaginatorInterface $paginator, Request $request) {
       
        $seller = $this->getUser()->getSeller()->getId();
        if (!$this->getUser()->getSeller()){
            return $this->redirectToRoute('app_login');
        }
        $queryBuilder = $this->entityManager->createQueryBuilder('o');
        $queryBuilder->select('o')
                    ->from(OrderDetails::class, 'o')
                    ->leftJoin('o.myOrder','m')
                    ->where('o.sellerId = :seller')
                    ->andWhere('m.isPaid = true')
                    ->groupBy('m.reference')
                    ->orderBy('m.created_at',"DESC")
                    ->setParameter('seller', $seller) 
            
        ;

        $query = $queryBuilder->getQuery();
        $orderDetails = $query->getResult();
        $orderDetails = $paginator->paginate(
            $orderDetails,
            // Define the page parameter
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('seller_space/orders.html.twig', [
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * @Route("/shop/orders/show/{id}/{reference}", name="seller_products_show")
    */
    public function showProducts($id,$reference) {
       

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        $products = array();

        foreach($order->getOrderDetails() as $detail) {
            if ($detail->getSellerId() == $id) {
                array_push($products,$detail);
            }
        }
        
        return $this->render('seller_space/show.html.twig', [
            'products' => $products,
            'order' => $order,
            'reference' => $reference
        ]);
    }

    /**
     * @Route("/shop/orders/update/{reference}", name="seller_order_update")
    */
    public function update($reference, Request $request,Order $order): Response {
       
        $form = $this->createForm(UpdateOrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($order);
            $this->entityManager->flush();
            return $this->redirectToRoute('seller_orders', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_space/order_update.html.twig', [
            
            'form' => $form,
        ]);
    }
}
