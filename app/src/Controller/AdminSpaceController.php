<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSpaceController extends AbstractController
{
    /**
     * @Route("/admin/space", name="app_admin_space")
    */

    public function index(): Response
    {
        return $this->render('admin_space/index.html.twig', [
            'controller_name' => 'AdminSpaceController',
        ]);
    }

    
}
