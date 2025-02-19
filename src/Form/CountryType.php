<?php

namespace App\Form;

use App\Entity\Continent;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('continent', EntityType::class, [
                'class' => Continent::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'Continent',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-3 w-25 fs-3'
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
