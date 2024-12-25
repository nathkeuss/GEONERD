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

class ForumPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['is_reply']) {
            // Ajouter le champ "title" uniquement si ce n'est pas une réponse
            $builder->add('title', TextType::class, [
                'label' => 'Titre du post',
            ]);
        }

        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu post forum',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image post forum',
                'mapped' => false,
                'required' => false,
            ])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostForum::class,
            'is_reply' => false,
        ]);
    }
}
