<?php

namespace App\Controller;

use DateTime;
use App\Entity\Photo;
use App\Entity\Product;
use App\Form\ExcelType;
use App\Entity\Category;
use App\Form\ProductType;
use App\Entity\Specification;
use App\Form\AdminProductType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    /**
     * @Route("/admin/products", name="admin_products")
     */
    public function list_product(): Response
    {
        
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        
        return $this->render('admin/admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/admin/create/product", name="admin_create_product")
     */
    public function admin_create_product(Request $request): Response
    {
       
        $product = new Product();
        $form = $this->createForm(AdminProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('coverPhoto')->getData();
            if ($cover) {
                
                $photo = md5(uniqid()) . '.' . $cover->guessExtension();

                $cover->move(
                    $this->getParameter('images_directory'),
                    $photo
                );
                $product->setCoverPhoto($photo);
           
            }
        
            foreach($product->getSpecifications() as $specification){
                if($specification->getSize() != null){
                
                    $specification->setSku($product->getId().$product->getCategory()->getTitle().$specification->getSize().$specification->getStock());
                        
                }
            }

            
          
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_products');

        }
        
        return $this->render('admin/admin_product/admin_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/create/product", name="create_product")
     */
    public function create_product(Request $request): Response
    {
       

        if(!$this->getUser()->getSeller() ) {
            return $this->redirectToRoute('app_login');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('coverPhoto')->getData();
            if ($cover) {
                
                $photo = md5(uniqid()) . '.' . $cover->guessExtension();

                $cover->move(
                    $this->getParameter('images_directory'),
                    $photo
                );
                $product->setCoverPhoto($photo);
           
            }
           
            $product->setSeller($this->getUser()->getSeller());
            // On definie la quantité de spécifiaction a 0
            $qteSpecifications = 0;
            // on boucle sur les spécifications du produit
            foreach($form->get('specifications')->getData() as $formSpecs){
                // on récupere la quantité de spécification et on l'incrémente
                $qteSpecifications += $formSpecs->getStock();
            }
            foreach($product->getSpecifications() as $specification){
                if($specification->getSize() != null){
                    // on envois la quantité des spécifications au produit
                    $product->setStock($qteSpecifications);
                    
                    // on génère le SKU du produit en fonction de la catégorie, de la taille et de la quantité de spécification du produit
                    $specification->setSku($product->getId().$product->getCategory()->getTitle().$specification->getSize().$specification->getStock());
                   
                    
                }
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('seller_products');

        }
        
        return $this->render('admin/admin_product/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/edit/product/{id}", name="edit_product")
     */
    public function edit_product(Request $request, Product $product): Response
    {
       
        $exCover = $product->getCoverPhoto();

        if( $this->getUser()->getRoles() !== ["ROLE_ADMIN"]) {
            return $this->redirectToRoute('app_login');
        }
        

        $form = $this->createForm(productType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('coverPhoto')->getData();
              $cover = $form->get('coverPhoto')->getData();
            if ($cover) {
                
            $photo = md5(uniqid()) . '.' . $cover->guessExtension();

                // On copie le fichier dans le dossier uploads
                $cover->move(
                    $this->getParameter('images_directory'),
                    $photo
                );
            $product->setCoverPhoto($photo);
            }
            
            if ($cover === null) {
               
                $product->setCoverPhoto($exCover);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('seller_products');

        }

        return $this->render('admin/admin_product/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/edit/product/{id}", name="admin_edit_product")
     */
    public function admin_edit_product(Request $request, Product $product): Response
    {
       
        $exCover = $product->getCoverPhoto();
 

        $form = $this->createForm(AdminProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('coverPhoto')->getData();
              $cover = $form->get('coverPhoto')->getData();
            if ($cover) {
                
            $photo = md5(uniqid()) . '.' . $cover->guessExtension();

                // On copie le fichier dans le dossier uploads
                $cover->move(
                    $this->getParameter('images_directory'),
                    $photo
                );
            $product->setCoverPhoto($photo);
            }
            
            if ($cover === null) {
               
                $product->setCoverPhoto($exCover);
              }

         
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_products');

        }

        return $this->render('admin/admin_product/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/shop/product/delete/{id}", name="delete_product")
     */
    public function delete_product($id)
    {
        $user = $this->getUser()->getSeller();

        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        $product = $this->entityManager->getRepository(Product::class)->findOneById($id);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('seller_products');            
    }

    /**
     * @Route("/admin/product/delete/{id}", name="admin_delete_product")
     */
    public function aaddelete_product($id)
    {


        $product = $this->entityManager->getRepository(Product::class)->findOneById($id);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('admin_products');            
    }

    /*
     * @Route("/shop/product/excel", name="excel_product")
     * @param Request $request
     * @return void
     */
    public function import_excel(Request $request)
    {
        $form = $this->createForm(ExcelType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file =  $form->get('file')->getData();
            // $file = $request->files->get('file'); // get the file from the sent request

            $fileFolder = __DIR__ . '/../../public/upload/excel/';  //choose the folder in which the uploaded file will be stored

            $filePathName = md5(uniqid()) . $file->getClientOriginalName();

            // apply md5 function to generate an unique identifier for the file and concat it with the file extension  
            try {
                $file->move($fileFolder, $filePathName);
            } catch (FileException $e) {
                dd($e);
            }

            $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file 
            $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line 
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
            //  dd($sheetData);

            $entityManager = $this->getDoctrine()->getManager();
            //  array_filter($sheetData, null);

            //  dd($sheetData);
            foreach ($sheetData as $key => $Row) {



                // dd(filter_var("bdsfsd45678fdsfds", FILTER_SANITIZE_NUMBER_INT));
                $title =  $Row['A'];
                $subtitle =  $Row['B'];
                $description =  $Row['C'];
                $price =  $Row['D'];
                $weight =  $Row['E'];
                $best_seller =  $Row['F'];
                $categorie =  $Row['G'];
                $image =  $Row['H'];
                $specification =  $Row['I'];
               


                    // On vide toutes lignes vides du tableau EXCEL
                if ($title === null && $categorie === null && $subtitle === null  && $weight === null && $price === null) {

                    unset($sheetData[$key]);
                } else {
                    // On vérifie que tous les champs obligatoires sont remplies
                    if ($title === null || $categorie === null || $description === null  || $price === null) {

                        $this->addFlash("Null", "Veuillez vérifier que tous les champs obligatoires sont remplies. Pour le reste vérifiez que les produits se sont bien ajoutés. ");
                    } else {

                        // On regarde si le produit existe deja afin de ne pas le créer en double

                        $produit = $entityManager->getRepository(Product::class)->findOneBy(array('title' => $title));

                        if (!$produit) {
                            $produit = new Product();
                        }
                          
                        $category = $entityManager->getRepository(Category::class)->findOneBy(array('title' => $categorie));
                            
                        if ($category === null) {
                            $category = new Category();
                            $category->setTitle($categorie);
                            $produit->setCategory($category);
                        } else {
                            $produit->setCategory($category);
                        }
                        $produit->setTitle($title);
                        $produit->setSubtitle($subtitle);
                        $produit->setDescription($description);
                        $produit->setPrice($price);
                        $produit->setWeight($weight);
                        $produit->setIsBest($best_seller);
                        // $produit->setSpecification($specification);
                        $produit->setSeller($this->getUser()->getSeller());
                        
                    
                        
                        // ICI ON VA S'OCCUPER DES PHOTOS

                        // $imageFilePath = $this->getParameter('images_directory');

                        // $objWorksheet = $spreadsheet->getSheet(0);

                        // $data = $objWorksheet->toArray();

                        // foreach ($objWorksheet->getDrawingCollection() as $drawing) {
                        //     list($startColumn, $startRow) = Coordinate::coordinateFromString($drawing->getCoordinates());
                        //     $idLigne = list($startColumn, $startRow) = Coordinate::coordinateFromString($drawing->getCoordinates());


                        //     $imageFileName = $drawing->getCoordinates() . mt_rand(1000, 9999);

                        //     $cell = $drawing->getCoordinates();
                        //     $keyPhoto = filter_var($cell, FILTER_SANITIZE_NUMBER_INT);

                           
                        //     // On récupère la cellule de la photo
                        //     $extension = $drawing->getPath();
                        //     // $image = imagecreatefromstring(file_get_contents($drawing->getPath()));
                          
                        //      $result = exif_imagetype($extension);
                        
                        //     if ($result == 2) {
                        //         $imageFileName .= '.jpg';
                        //         $source = imagecreatefromjpeg($drawing->getPath());
                        //         imagejpeg($source, $imageFilePath . $imageFileName);

                        //         if ($key == $keyPhoto) {
                        //             if ($imageFileName !== $produit->getCoverPhoto()) {
                        //                 $photos = new Photo();
                        //                 $photos->setNom($imageFileName);
                        //                 $declinaison->addPhoto($photos);
                        //             }
                        //             // dd($imageFileName . " = " . $produit->getCoverPhoto());
                                    
                        //            if (!$produit->getCoverPhoto()) {
                        //             $produit->setCoverPhoto($imageFileName);
                        //            }
                                   
                        //         }
                        //     } else if ($result = 3) {
                        //         // On crée notre image à partir du lien
                        //         $source = imagecreatefrompng($drawing->getPath());

                        //         // On crée une image pour mettre un fond blanc
                        //         $width =  imagesx($source);
                        //         $height = imagesy($source);

                        //         $dest = imagecreatetruecolor($width, $height);
                        //         // $white = imagecolorallocate($dimg, 255, 255, 255);
                        //         // $img = imagecolortransparent($dimg, $white);
                        //       imagecopyresampled($dest, $source, 0, 0, 0, 0, $width, $height, $width, $height);
                        //       imagesavealpha($dest, true);
                        //       $trans_colour = imagecolorallocatealpha($dest, 255, 255, 255, 127);
                        //       imagefill($dest, 0, 0, $trans_colour);

                        //         $imageFileName .= '.png';
                        //         imagepng($dest, $imageFilePath . $imageFileName, 1);

                        //         if ($key == $keyPhoto) {
                                   
                        //             $produit->setCoverPhoto($imageFileName);
                        //         }
                        //     }
                        //     $startColumn = $this->ABC2decimal($startColumn);
                        //     $data[$startRow - 1][$startColumn] = $imageFilePath . $imageFileName;
                        // }

                        $entityManager->persist($produit);
                        $entityManager->flush();
                        $this->addFlash("Good", "Fichier ajouté avec succès");
                    }
                }
            }
        }
        return $this->render('admin/admin_product/import_products.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
