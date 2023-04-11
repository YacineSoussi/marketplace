<?php

namespace App\Controller;

use App\Entity\ProductLike;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountWishController extends AbstractController
{
    private $entityManager;
    private $session;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/wish-list", name="account_wish_list")
     */
    public function index(): Response
    {
        $user = $this->getUser();

        //récuperer tous les likes de l'utilisateur courant
        $wishlist = $this->entityManager->getRepository(ProductLike::class)->findWishListUser($user);

        $this->session->set('wishlist_user', $wishlist);


        return $this->render('account/wish_list.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }
}
