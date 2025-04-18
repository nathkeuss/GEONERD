<?php

namespace App\Form\User\Profile;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Laissez vide pour ne pas modifier le mot de passe',
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Ton pseudo',
            ])
            ->add('profile_picture', FileType::class, [
                'label' => 'Ta photo de profil',
                'required' => false,
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
