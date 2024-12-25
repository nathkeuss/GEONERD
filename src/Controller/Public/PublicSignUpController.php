<?php

namespace App\Controller\Public;

use App\Entity\User;
use App\Form\SignUpType;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PublicSignUpController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function signUp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user = new User();

        $formSignUp = $this->createForm(SignUpType::class, $user);

        $formSignUp->handleRequest($request);

        if ($formSignUp->isSubmitted() && $formSignUp->isValid()) {

            $realPassword = $formSignUp->get('password')->getData();

            $passwordHashed = $userPasswordHasher->hashPassword($user, $realPassword);

            $user->setPassword($passwordHashed);

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $formSignUpView =  $formSignUp->createView();

        return $this->render('public/signup/signup.html.twig', [
            'formSignUpView' => $formSignUpView,
        ]);

    }

}