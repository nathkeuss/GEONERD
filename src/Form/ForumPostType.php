<?php

namespace App\Form;

use App\Entity\PostForum;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ForumPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['is_reply']) {
            // Ajouter le champ "title" uniquement si ce n'est pas une réponse
            $builder->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Titre de la discussion',
                    'class' => 'title-post py-2 px-2 w-100'
                ],
            ]);
        }

        $builder
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Entrez votre message...',
                    'class' => 'message-post py-2 px-2 w-100',
                    'required' => true
                ],
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'image-post w-100',
                    'placeholder' => 'Choisissez une image'
                ],
                'constraints' => [
                    new File([
                    'maxSize' => '8M',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Choisissez un format png/jpeg/webp',
                    ])
                ]
            ])
            ->add('Publier', SubmitType::class, [
                'attr' => [
                    'class' => 'button-publish mb-4'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostForum::class,
            'is_reply' => false,
        ]);
    }
}
