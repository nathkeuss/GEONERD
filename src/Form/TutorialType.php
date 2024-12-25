<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Tutorial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutorialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require']
            ])
            ->add('backgroundImage', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require']
            ])
            ->add('title')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
            ])
            ->add('Valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
            'is_require' => false,
        ]);
    }
}
