<?php

namespace App\Controller\User\Profile;

use App\Form\User\Profile\ProfileType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/user/{id}/profile', name: 'app_user_profile_show', methods: ['GET', 'POST'])]
    public function showAndUpdateProfile(int                         $id,
                                         Request                     $request,
                                         ImageUploader               $imageUploader,
                                         EntityManagerInterface      $entityManager,
                                         UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getUser();

        if ($user->getId() !== $id) {
            $this->addFlash('danger', 'Tu ne peux pas accéder à ce profil');
            return $this->redirectToRoute('home');
        }

        $formProfile = $this->createForm(ProfileType::class, $user);
        $formProfile->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {

            $userUsername = $formProfile->get('username')->getData();
            $userPassword = $formProfile->get('password')->getData();
            $userProfilePicture = $formProfile->get('profile_picture')->getData();

            if ($userUsername) {
                $user->setUsername($userUsername);
            }

            if ($userPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $userPassword));
            }

            if ($userProfilePicture) {
                $imageNewFilename = $imageUploader->uploadImage($userProfilePicture, 'user/profile_pictures');

                if ($imageNewFilename) {
                    $imageUploader->removeImage('user/profile_pictures', $user->getProfilePicture());
                    $user->setProfilePicture($imageNewFilename);
                }
            }


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Ton profil a bien été mis à jour');
            return $this->redirectToRoute('app_user_profile_show', ['id' => $id]);
        }

        $formProfileView = $formProfile->createView();

        return $this->render('user/profile/show.html.twig', [
            'formProfileView' => $formProfileView,
        ]);

    }

}