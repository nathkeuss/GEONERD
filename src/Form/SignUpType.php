<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('username', TextType::class, [
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => $options['is_registration'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entre un mot de passe'
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.',
                    ])
                ]
            ]);
         if ($options['include_profile_picture']) {
             $builder->add('profilePicture', FileType::class, [
                 'required' => false,
                 'mapped' => false,
                 'label' => 'Photo de profil',
                 'constraints' => [
                     new File([
                         'maxSize' => '8M',
                         'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg', 'image/svg'],
                         'maxSizeMessage' => 'La taille maximale de l\'image est de 8 Mo',
                         'mimeTypesMessage' => 'Seules les images jpeg, png, jpg et svg sont autorisées',
                     ])
                 ]
             ]);
         }

        $builder->add('submit', SubmitType::class, [
            'label' => $options['submit_label'] ?? 'Valider',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'include_profile_picture' => false,
            'submit_label' => 'Valider',
            'is_registration' => true,
        ]);
    }
}
