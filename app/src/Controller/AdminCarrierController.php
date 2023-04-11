<?php

namespace App\Controller;

use App\Entity\Carrier;
use App\Form\CarrierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminCarrierController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/carriers", name="list_carrier")
     */
    public function list_carrier(): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $carriers = $this->entityManager->getRepository(Carrier::class)->findAll();

        return $this->render('admin/admin_carrier/index.html.twig', [
            'carriers' => $carriers,
        ]);
    }

    /**
     * @Route("/admin/create/carrier", name="create_carrier")
     */
    public function create_carrier(Request $request): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $carrier = new Carrier();
        $form = $this->createForm(CarrierType::class,$carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $this->entityManager->persist($carrier);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_carrier');

        }
        
        return $this->render('admin/admin_carrier/form.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/edit/carrier/{id}", name="edit_carrier")
     */
    public function edit_carrier(Request $request, Carrier $carrier): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }
        

        $form = $this->createForm(CarrierType::class,$carrier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          
            $this->entityManager->persist($carrier);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_carrier');

        }

        return $this->render('admin/admin_carrier/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
    

    /**
     * @Route("/admin/carrier/delete/{id}", name="delete_carrier")
     */
    public function delete_carrier($id)
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $carrier = $this->entityManager->getRepository(Carrier::class)->findOneById($id);
        $this->entityManager->remove($carrier);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('list_carrier');            
    }
}
