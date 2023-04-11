<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'Prénom'],
                'disabled' => true
           ])
           ->add('lastname', TextType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'Nom'],
                'disabled' => true
           ])
           ->add('email', EmailType::class,[
                'label' => ' ',
                'attr'=> ['placeholder' => 'E-mail'],
                'disabled' => true
           ])           
           ->add('old_password', PasswordType::class,[
                'mapped' => false,
                'label' => ' ',
                'attr'=> ['placeholder' => 'Mot de passe actuel'],
            ])
           ->add('new_password', RepeatedType::class,[// sert 2 deux fois( first_option, second_option)
               'type' => PasswordType::class,
               'mapped' => false,
               'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
               'required' => true,
               'first_options' => ['label' => ' ', 'attr' => ['placeHolder' => 'Nouveau mot de passe']],
               'second_options' => ['label' => ' ', 'attr' => ['placeHolder' => 'Confirmer le nouveau mot de passe']]
         ])
           ->add('submit', SubmitType::class,[
               'label' => 'Mettre à jour',
               'attr'=> [
                   'class' => 'btn_3'
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
