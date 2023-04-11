<?php

namespace App\Form;

use App\Entity\Seller;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\SpecificationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('isBest', ChoiceType::class,[
                'label' => 'Mettre en avant ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ]
            ])
            ->add('price')
            ->add('coverPhoto', FileType::class, [
                'data_class' => null,
                'label' => "Photo",
                'required' => true
            ])
            ->add('weight')
            ->add('promo', ChoiceType::class,[
                'label' => 'Promotion ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ]
            ])
            ->add('specifications', CollectionType::class, [
                'entry_type' => SpecificationType::class,
                'label' => " ",
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
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
            ->add('seller', EntityType::class, [
                'class' => Seller::class,
                'label' => "Vendeur",
                'required' => true,
                'expanded' => false,
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
            ])
            ->add('submit',SubmitType::class,[
                'label' => "Modifier",
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
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
