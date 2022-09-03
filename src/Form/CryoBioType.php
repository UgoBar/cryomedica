<?php

namespace App\Form;

use App\Entity\CryoBio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CryoBioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstText', TextareaType::class, [
                'label' => 'Premier paragraphe',
                'required' => false
            ])
            ->add('secondText', TextareaType::class, [
                'label' => 'Second paragraphe',
                'required' => false
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => false,
                'required' => false
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
            'data_class' => CryoBio::class,
        ]);
    }
}
