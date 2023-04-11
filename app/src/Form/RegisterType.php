<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class,[
                'attr' => array(
                    'placeholder' => 'Prénom'
                ),
                
                'label' => 'Prénom :'
            ])
            ->add('lastname', TextType::class,[
                'attr' => array(
                    'placeholder' => 'Nom'
                ),
                'label' => 'Nom :'
            ])
            ->add('email', EmailType::class,[
                'attr' => array(
                    'placeholder' => 'Email'
                ),
                'label' => 'Email :',
                ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_name' => 'pass',
                'second_name' => 'confirm',
                'invalid_message' => "Veuillez saisir un mot de passe identique",
                'label'=>"Mot de passe :",
                'first_options' => ['attr' => array(
                    'placeholder' => 'Mot de passe'
                ),'label' => 'Mot de passe'],
                'second_options' => ['attr' => array(
                    'placeholder' => 'Confirmez votre mot de passe'
                ),'label' => 'Confirmez votre mot de passe'],
                
            ])
            ->add('submit', SubmitType::class,[
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'btn_3'
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
