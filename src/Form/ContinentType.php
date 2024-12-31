<?php

namespace App\Form;

use App\Entity\Continent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContinentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control', // Style Bootstrap pour les champs
                    'placeholder' => 'Nom du continent', // Ajout d'un placeholder
                ],
                'label_attr' => [
                    'class' => 'form-label', // Style Bootstrap pour les labels
                ],
                'label' => 'Nom du continent', // Texte du label
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary w-25 fs-3', // Style Bootstrap pour le bouton
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Continent::class,
        ]);
    }
}
