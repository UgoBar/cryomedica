<?php

namespace App\Form;

use App\Entity\CryoAbout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CryoAboutType extends AbstractType
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
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CryoAbout::class,
        ]);
    }
}
