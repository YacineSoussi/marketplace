<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) // $options[] ?? => dd($options)
    {
        
        $user = $options['user'];
        $builder
            ->add('addresses', EntityType::class,[
                'class' => Address::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices' => $user->getAddresses(),
                'attr' => ['class' => ".checkout-item"]
            ])
            ->add('carriers', EntityType::class,[
                'class' => Carrier::class,
                'label'=> 'Choisissez votre transporteur',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'attr' => ['class' => ".checkout-item mt-4 mb-4"]
            ])
            
			->add('submit', SubmitType::class,[
                'label' => 'Valider ma commande',
                'attr' => ['class' => "button button-payment mt-4 mb-4"]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => array()
        ]);
    }
}
