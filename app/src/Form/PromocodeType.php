<?php

namespace App\Form;

use App\Entity\Promocode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PromocodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('promoCodeName', TextType::class,[
                'label' => 'Intitulé'
            ])
            ->add('percent', TextType::class,[
                'label' => "Pourcentage de réduction"
            ])
            ->add('promoCodeTitle', TextType::class,[
                'label' => 'Description'
            ])
            ->add('isActive', ChoiceType::class,[
                'label' => 'Est actif ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ]
            ])
            ->add('endDate', DateType::class, [
                'label'=>"Fin de validité",
                'attr' => ['placeholder' => 'Date de fin'],
                'widget' => 'single_text',
                
            ])
            ->add('useNumber', TextType::class,[
                'label' => ''
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promocode::class,
        ]);
    }
}
