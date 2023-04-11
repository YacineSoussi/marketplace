<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class,[
            'label' => 'Prénom :'
        ])
        ->add('lastname', TextType::class,[
            'label' => 'Nom :'
        ])
        ->add('roles', ChoiceType::class, [
                'choices' => ['ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER', 'ROLE_CUSTOMER' => 'ROLE_CUSTOMER'],
                'expanded' => true,
                'multiple' => true,
            ]
        )
        ->add('email', EmailType::class,[
            'label' => 'Email :',
            ])
        
        ->add('submit', SubmitType::class,[
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
            'data_class' => User::class,
        ]);
    }
}
