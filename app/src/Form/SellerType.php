<?php

namespace App\Form;

use App\Entity\Seller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address',TextType::class,[
                'attr' => array(
                    'placeholder' => "Adresse de l'entreprise"
                )
            ])
            ->add('contactMail',EmailType::class,[
                'attr' => array(
                    'placeholder' => 'E-mail de contact'
                )
            ])
            // ->add('label',TextType::class,[
            //     'attr' => array(
            //         'placeholder' => ''
            //     )
            // ])
            ->add('legalBrand',TextType::class,[
                'attr' => array(
                    'placeholder' => 'Le nom légal de votre entreprise'
                )
            ])
            ->add('type',TextType::class,[
                'attr' => array(
                    'placeholder' => "Type d'activité"
                )
            ])
            ->add('offerDescription',TextareaType::class,[
                'attr' => array(
                    'placeholder' => 'Quel type de produits/services offre votre entreprise ?'
                )
            ])
            ->add('phoneContact',TextType::class,[
                'attr' => array(
                    'placeholder' => 'Télephone de contact'
                )
            ])
            ->add('firstnameCEO',TextType::class,[
                'attr' => array(
                    'placeholder' => 'Nom du représentant de votre entreprise'
                )
            ])
            ->add('lastnameCEO',TextType::class,[
                'attr' => array(
                    'placeholder' => 'Prénom du représentant de votre entreprise'
                )
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Je deviens vendeur',
                'attr' => ['class' => "button button-payment mt-4 mb-4"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
