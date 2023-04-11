<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('firstname', TextType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'Prénom']
            ])
            ->add('lastname', TextType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'Nom']
            ])
            ->add('address', TextType::class,[
                'label' => ' ',
                'attr'=> [
                    'placeholder' => 'Adresse',
                ]
            ])
            ->add('addressInformation', TextareaType::class,[
                'label' => ' ',
                'attr'=> [
                    'placeholder' => "Complément d'information",
                ]
            ])
            ->add('phone', TelType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'Téléphone']
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Valider',
                'attr'=> ['class' => 'btn_3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
