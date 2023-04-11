<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Data\SearchData;
use App\Entity\ProductLike;
use App\Form\SearchFormType;
use App\Entity\Specification;
use App\Form\CommentFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductLikeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/products", name="list_products")
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $products = $productRepository->findSearch($data);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}/{id}", name="show_product")
     */
    public function show(SessionInterface $session, Request $request, $slug, $id)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('products');
        }

        $session = $request->getSession();
        $attributSession = $session->get('attributSession', []);

        $formAttribut = $this->createFormBuilder()
            ->add('size', EntityType::class, [

                'label' => "Choisissez votre taille",
                'required' => true,
                'class' => Specification::class,
                'choices' => $product->getSpecifications(),
                'multiple' => false,
                'expanded' => true,
                "mapped" => false,
            ])

            ->add('save', SubmitType::class, [
                'label' => "Ajouter au panier",
                'attr' => ['class' => 'btn_3'],
            ])
            ->getForm();

        $formAttribut->handleRequest($request);
        if ($formAttribut->isSubmitted() && $formAttribut->isValid()) {

            $sizeArray = $formAttribut['size']->getData();
            $size = null;
            $idProduct = null;
           
            if ($product->getSpecifications()->getOwner()->getSpecifications()->getValues() == null) {
                $idProduct = $product->getId();
            } else {
                $idProduct = $sizeArray->getSku();
                $size = $sizeArray->getSize();
            }
            $attributSession[$idProduct] = [
                'idProduct' => $idProduct,
                'product' => $product->getDetails(),
                'size' => $size,
            ];
            $session->set('attributSession', $attributSession);
            return $this->redirectToRoute("add_to_cart", [

                'id' => $idProduct,
            ]);
        }
        // On gère ici la partie commentaire
        $comment = new Comment();
        $formComment = $this->createForm(CommentFormType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {

            $user = $this->getUser();
            $comment->setAuteur($user);
            $comment->setProduct($product);
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'formAttribut' => $formAttribut->createView(),
            'formComment' => $formComment->createView()
        ]);
    }

    /**
     * @Route("/comment/delete/{id}", name="delete_comment")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return void
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager, Request $request)
    {

        $entityManager->remove($comment);
        $entityManager->flush($comment);

        $redirectToBack = $request->headers->get('referer');

        return $this->redirect($redirectToBack);
    }


    /**
     * Permet de liker ou unliker un produit
     * @param Product $product
     * @param EntityManagerInterface $entityManager
     * @param ProductLikeRepository $likeRepo
     * @return Response
     * 
     * @Route("/product/{slug}/{id}/like", name="product_like")
     */
    public function like(Product $product, EntityManagerInterface $entityManager, ProductLikeRepository $likeRepo): Response{

        $user = $this->getUser();

        //si l'utilisateur n'est pas connecté retourné une erreur 403
        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorised"
        ], 403);


        //si l'utilisateur supprime son like
        if($product->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                'product' => $product,
                'user' => $user
            ]);

            $entityManager->remove($like);
            $entityManager->flush($like);

            return $this->json([
                'code' => 200,
                'message' => "Like bien supprimé.",
                'likes' => $likeRepo->count(['product' => $product]),
                'likes_user' => $likeRepo->count(['user' => $user])
            ], 200);
        }


        $like = new ProductLike();
        $like->setProduct($product)->setUser($user);

        $entityManager->persist($like);
        $entityManager->flush($like);

        return $this->json([
            'code' => 200, 
            'message' => "Like bien ajouté !",
            'likes' => $likeRepo->count(['product' => $product]),
            'likes_user' => $likeRepo->count(['user' => $user])
        ], 200);
    }
}
