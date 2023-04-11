<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\ProductLike;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
    
        for ($i = 1; $i <= 30; $i++) {

            
                $user = new User();
                $user
                ->setFirstname("Pierre")
                ->setLastname("BOITELLE")
                ->setEmail("pierre" . $i . "@esgi.com")
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setIsActif(true);
                ;
            
            $users[] = $user;

            // Gestion des categories
            $category = new Category();
            $category->setTitle($faker->sentence());
            
            // Gestion des products
            $product = new Product();
            $title = $faker->sentence();
            $contenu = join('</p><p>', $faker->paragraphs(5));

            $product->setTitle($title)
                ->setDescription($contenu)
                ->setPrice(mt_rand(40, 200))
                ->setCoverPhoto("https://global-uploads.webflow.com/5d552994548be47b97db38c2/5deab13bf88fbc95f8271e6e_11-2.jpg")
                ->setCategory($category);
                if(mt_rand(0,1)) {
                    $product->setPromo(true);
                }

            if(mt_rand(0,1)) {
                $comment = new Comment();
                $comment
                ->setContent($faker->paragraph())
                ->setRating(mt_rand(1,5))
                ->setAuteur($user)
                ->setProduct($product)
                ;
                $manager->persist($comment);
            }

            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $like = new ProductLike();
                $like->setProduct($product)
                    ->setUser($faker->randomElement($users));

                $manager->persist($like);
            }

            // for ($j = 1; $j <= mt_rand(2, 5); $j++) {
            //     $image = new Image();
            //     $image->setImage($faker->imageUrl());

            //     $image->setProduct($product);

            //     $manager->persist($image);
            // }

            $manager->flush();
        }
    }
}
