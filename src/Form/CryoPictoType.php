<?php

namespace App\Form;

use App\Entity\CryoPicto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CryoPictoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('color')
            ->add('bg_color')
            ->add('position', IntegerType::class, [
                'label' => 'Afficher la bannière en position',
                'required' => false
            ])
            ->add('page', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Accueil' => 'home',
                    'Cabines électriques' => 'elec',
                ]
            ])
            ->add('media', CryoMediaType::class, [
                'label' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CryoPicto::class,
        ]);
    }
}
