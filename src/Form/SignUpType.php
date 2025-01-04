<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'required' => true,
            ])
            ->add('username', null, [
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => $options['is_registration'],
            ]);
         if ($options['include_profile_picture']) {
             $builder->add('profilePicture', FileType::class, [
                 'required' => false,
                 'mapped' => false,
                 'label' => 'Photo de profil',
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
