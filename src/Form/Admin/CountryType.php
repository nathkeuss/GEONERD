<?php

namespace App\Form\Admin;

use App\Entity\Country;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom du pays'
                ]
            ])
            ->add('flag', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Drapeau du pays'
                ],
                'label' => 'Drapeau du pays',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('banner', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bannière du pays'
                ],
                'label' => 'Bannière du pays',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary mt-3 w-25 fs-5'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
            'is_require' => false,
        ]);
    }
}
