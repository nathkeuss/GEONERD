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
                'required' => !$options['is_require'],
                'label' => 'Image du tutoriel',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('backgroundImage', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require'],
                'label' => 'Image de fond',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('title', null, [
                'label' => 'Titre du tutoriel',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'label' => 'Pays associé',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mt-3 w-25 fs-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
            'is_require' => false,
        ]);
    }
}
