<?php

namespace App\Controller\User;

use App\Form\SignUpType;
use App\Repository\UserRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/{id}/profile', name: 'profile')]
    public function showAndUpdateAccount(UserRepository $userRepository,Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getUser();

        $formUpdateAccount = $this->createForm(SignUpType::class, $user, [
            'include_profile_picture' => true,
            'is_registration' => false,
            'submit_label' => 'Mettre à jour',
        ]);

        $formUpdateAccount->handleRequest($request);

        if ($formUpdateAccount->isSubmitted() && $formUpdateAccount->isValid()) {

            $newPassword = $formUpdateAccount->get('password')->getData();

            if($newPassword){
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $userProfilePicture = $formUpdateAccount->get('profilePicture')->getData();

            if($userProfilePicture){
                $originalFilename = $userProfilePicture->getClientOriginalName();
                $fileExtension = $userProfilePicture->guessExtension();
                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($originalFilename, $fileExtension);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_profile';

                $userProfilePicture->move($imgDir, $newFilename);

                if($user->getProfilePicture() && $user->getProfilePicture() !== 'default-profile.png'){
                    $oldFile = $imgDir . '/' . $user->getProfilePicture();
                    if(file_exists($oldFile)){
                        unlink($oldFile);
                    }
                }

                $user->setProfilePicture($newFilename);
            } else {
                $user->setProfilePicture($user->getProfilePicture());
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        $formUpdateAccountView = $formUpdateAccount->createView();

        return $this->render('/user/profile/show_profile.html.twig', [
            'user' => $user,
            'formUpdateAccountView' => $formUpdateAccountView,
        ]);

    }
}