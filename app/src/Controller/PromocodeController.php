<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Promocode;
use App\Form\PromocodeType;
use App\Form\AdminPromocodeType;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PromocodeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/admin/promocodes", name="admin_list_promocodes")
    */
    public function list_promocode_all(): Response
    {
        $user = $this->getUser();

        if(!$user ) {
            return $this->redirectToRoute('app_login');
        }

        $promocodes = $this->entityManager->getRepository(Promocode::class)->findAll();

        return $this->render('promocode/index.html.twig', [
            'promocodes' => $promocodes,
        ]);
    }
    
    /**
     * @Route("/shop/promocodes", name="list_promocodes")
    */
    public function list_promocode(): Response
    {
        $user = $this->getUser();

        if(!$user ) {
            return $this->redirectToRoute('app_login');
        }

        $promocodes = $this->entityManager->getRepository(Promocode::class)->findBy([
            "sellerId" => $this->getUser()->getSeller()->getId(),
        ]);

        return $this->render('promocode/index.html.twig', [
            'promocodes' => $promocodes,
        ]);
    }

    /**
     * @Route("/shop/create/promocode", name="create_promocode")
     */
    public function create_promocode(Request $request): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $promocode = new Promocode();
        $form = $this->createForm(PromocodeType::class,$promocode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $promocode->setSellerId($this->getUser()->getSeller()->getId());
            $this->entityManager->persist($promocode);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_promocodes');

        }
        
        return $this->render('promocode/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/create/promocode", name="admin_create_promocode")
     */
    public function admin_create_promocode(Request $request): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $promocode = new Promocode();
        $form = $this->createForm(AdminPromocodeType::class,$promocode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $promocode->setSellerId($this->getUser()->getSeller()->getId());
            $this->entityManager->persist($promocode);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_promocodes');

        }
        
        return $this->render('promocode/admin_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/edit/promocode/{id}", name="edit_promocode")
     */
    public function edit_promocode(Request $request, Promocode $promocode): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }
        

        $form = $this->createForm(PromocodeType::class,$promocode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          
            $this->entityManager->persist($promocode);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_promocodes');

        }

        return $this->render('promocode/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/edit/promocode/{id}", name="admin_edit_promocode")
     */
    public function admin_edit_promocode(Request $request, Promocode $promocode): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $form = $this->createForm(PromocodeType::class,$promocode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          
            $this->entityManager->persist($promocode);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_list_promocodes');

        }

        return $this->render('promocode/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/promocode/delete/{id}", name="delete_promocode")
    */
    public function delete_promocode($id): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $promocode = $this->entityManager->getRepository(Promocode::class)->findOneById($id);
        $this->entityManager->remove($promocode);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('list_promocodes');            
    }

    /**
     * @Route("/admin/promocode/delete/{id}", name="admin_delete_promocode")
    */
    public function admin_delete_promocode($id): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $promocode = $this->entityManager->getRepository(Promocode::class)->findOneById($id);
        $this->entityManager->remove($promocode);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('admin_list_promocodes');            
    }

    
    /**
    * @Route("/api/promocodes", methods={"GET"}, name="api_promocode")
    */
    public function getValidPromocodesAction()
    {
        $promocodes = $this->entityManager->getRepository(Promocode::class)->findAll();
        $now = (new \DateTime())->format('Y-m-d');
        $arrayCode = array();
        foreach ($promocodes as $promocode) {
            if (($promocode->getEndDate()->format('Y-m-d') >= $now) && ($promocode->getIsActive() == true)) {
                array_push($arrayCode, $promocode);
            }
        }

        return $this->json($arrayCode);
    }


    
    /**
     * @Route("/order/{reference}/promotional/add", name="order_promo",methods={"PUT","GET"})
     */
    public function updateOrderWithPromo($reference,Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        $reduce = null;
        foreach ($order->getOrderDetails() as $detail) {
            if ($detail->getSellerId() == $data['sellerId'] ) {
                $discount = ($detail->getPrice()*$data['discountPercent'])/100;
                $newProductPrice = $detail->getPrice() - $discount;
                $detail->setPrice($newProductPrice);
                $detail->setTotal($newProductPrice * $detail->getQuantity());
                $reduce += $discount;
            }        
        }
       
        $order->setTotal($order->getTotal()-$reduce);
        $order->setIsPromocode(true);
        $order->setPromocodename($data['promoCodeName']);


        $this->entityManager->persist($order);
        $this->entityManager->flush();
        dd('order '.$reference.' was updated');
    }
}
