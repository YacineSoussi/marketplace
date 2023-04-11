<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\Order1Type;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/order")
 */
class OrderAdminController extends AbstractController
{
    /**
     * @Route("/all", name="app_order_admin_index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository,PaginatorInterface $paginator,Request $request): Response
    {
       
        $orders = $paginator->paginate(
            $orderRepository->findBy(['isPaid' => 1]),
            // Define the page parameter
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('order_admin/index.html.twig', [
            'orders' => $orders,
        ]);
    }


    /**
     * @Route("/{id}", name="app_order_admin_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('order_admin/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_order_admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(Order1Type::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->add($order);
            return $this->redirectToRoute('app_order_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order_admin/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_order_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order);
        }

        return $this->redirectToRoute('app_order_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
