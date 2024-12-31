<?php

namespace App\Form;

use App\Entity\Tutorial;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\TutorialPart;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutorialPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le contenu ici...'
                ],
                'label' => 'Contenu',
                'label_attr' => [
                    'class' => 'form-label fw-bold'
                ]
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => !$options['is_require'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Choisissez une image'
                ],
                'label' => 'Image associée',
                'label_attr' => [
                    'class' => 'form-label fw-bold'
                ]
            ])
            ->add('tutorial', EntityType::class, [
                'class' => Tutorial::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'Tutoriel associé',
                'label_attr' => [
                    'class' => 'form-label fw-bold'
                ]
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary w-100 mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TutorialPart::class,
            'is_require' => false,
        ]);
    }
}
