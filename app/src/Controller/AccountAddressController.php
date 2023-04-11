<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;


use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AccountAddressController extends AbstractController
{
    private $entityManager;
	public function __construct(
        EntityManagerInterface $entityManager
    ) {
		$this->entityManager = $entityManager;	
	}

    /**
     * @Route("/account/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
        //up
    }

    /**
    * @Route("/account/edit/address/{id}", name="account_address_edit")
    */
    public function edit(Request $request,$id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        $form = $this->createForm(AddressType::class, $address);

        if (!$address || $address->getUser() !== $this->getUser()) {
            return $this->redirect('account_address');
        }

		$form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');
            
        }
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
    * @Route("/account/delete/address/{id}", name="account_address_delete")
    */
    public function delete($id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() === $this->getUser()) {
            $this->entityManager->remove($address);
			$this->entityManager->flush();
        }
        return $this->redirectToRoute('account_address');
    }

    /**
    * 
    * When registrated user try to order but didn't fill out an address
    * 
    * @Route("/account/add/address", name="account_address_add")
    */

    public function add(Request $request, CartService $cart): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

		$form = $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {

            $address->setuser($this->getUser());
            if($address){
                $this->entityManager->persist($address);
				$this->entityManager->flush();
                if($cart->get()){
                    return $this->redirectToRoute('order');
                }else{
                    return $this->redirectToRoute('account_address');
                }
                    
            }
        }
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
