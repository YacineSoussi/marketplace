<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Order1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference')
            ->add('created_at', DateType::class, [
                'label'=>"Crée le",
                'attr' => ['placeholder' => 'Date de fin'],
                'widget' => 'single_text',
                
            ])
            ->add('carrierName', TextType::class, [
                'label' => 'Transporteur'
            ])
            ->add('carrierPrice', TextType::class, [
                'label' => 'Frais de port'
            ])
            ->add('delivery', TextareaType::class, [
                'label' => 'Informations'
            ])
            ->add('isPaid', ChoiceType::class,[
                'label' => 'Payée ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
            ->add('stripeSessionId', TextType::class, [
                'label' => 'Session stripe'
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    "Payée" => '1' ,
                    "En cours de livraison" => '2' ,
                    "Livrée" => '3' 

                ],
            ])
            ->add('trackNumber', TextType::class, [
                'label' => 'Numéro de suivi'
            ])
            ->add('total', TextType::class, [
                'label' => 'Total'
            ])
            ->add('promocodename', TextType::class, [
                'label' => 'Label du promocode'
            ])
            ->add('isPromocode', ChoiceType::class,[
                'label' => 'Promocode utilisé ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
