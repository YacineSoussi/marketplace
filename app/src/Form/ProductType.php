<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\SpecificationType;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function __construct(
        // ProductRepository $productRepository,
        CategoryRepository $categoryRepository
        )
    {
        // $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                "label" => "Nom du produit" 

            ])
            ->add('description',TextareaType::class,[
                "label" => "Description"
            ])
            ->add('weight', NumberType::class, [
                "label" => "Poids (g)"
            ])
            ->add('price', NumberType::class, [
                "label" => "Prix (€)"
            ])
            ->add('isBest', ChoiceType::class, [
                'required' => false,
                'label' => 'Afficher en première page',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => "Catégorie",
                'required' => true,
                'expanded' => false,
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
                
            ])
            ->add('promo', ChoiceType::class,[
                'required' => false,
                'label' => "Promotion ?",
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ]
            ])

            ->add('coverPhoto', FileType::class, [
                'data_class' => null,
                'label' => "Illustration",
                'required' => false
            ])

            

            ->add('specifications', CollectionType::class, [
                'entry_type' => SpecificationType::class,
                'label' => " ",
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])

            ->add('submit',SubmitType::class,[
                'label' => "Ajouter",
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->add('stock', NumberType::class, [
                'required' => false,
                ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
