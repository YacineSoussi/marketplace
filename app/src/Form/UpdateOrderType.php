<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UpdateOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('delivery')
            ->add('isPaid', ChoiceType::class,[
                'label' => 'Payée ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    "Payée" => '1' ,
                    "En cours de livraison" => '2' ,
                    "Livrée" => '3' 

                ],
            ])
            ->add('trackNumber')
            ->add('submit', SubmitType::class,[
                'label' => 'Mettre à jour',
                'attr' => ['class' => "btn btn-primary"]
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
