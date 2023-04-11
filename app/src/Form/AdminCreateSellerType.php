<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Seller;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminCreateSellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class,[
                'label' => 'Adresse'
            ])
           
            ->add('legalBrand', TextType::class,[
                'label' => 'Nom légal'
            ])
            ->add('type', TextType::class,[
                'label' => 'Type'
            ])
            ->add('offerDescription', TextareaType::class,[
                'label' => "Description de l'activité"
            ])
            ->add('phoneContact', TextType::class,[
                'label' => 'Téléphone'
            ])
           
            ->add('isActif', ChoiceType::class,[
                'label' => 'Actif ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
            ->add('isRequested', ChoiceType::class,[
                'label' => 'Demande effectuée ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => "Utilisateur",
                'required' => true,
                'expanded' => false,
                'label_attr' => array(
                    'class' => 'radio-inline'
                ),
            ])

            ->add('submit', SubmitType::class,[
                'label' => "Ajouter",
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
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
