<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/admin/categories", name="list_category")
     */
    public function list_category(): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->render('admin/admin_category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/create/category", name="admin_create_category")
     */
    public function create_category(Request $request): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_category');

        }
        
        return $this->render('admin/admin_category/add_category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/edit/category/{id}", name="admin_edit_category")
     */
    public function edit_category(Request $request, Category $category): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }
        

        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
          
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('list_category');

        }

        return $this->render('admin/admin_category/add_category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="delete_category")
     */
    public function delete_category($id): Response
    {
        $user = $this->getUser();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $category = $this->entityManager->getRepository(Category::class)->findOneById($id);
        $this->entityManager->remove($category);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('list_category');            
    }
}
