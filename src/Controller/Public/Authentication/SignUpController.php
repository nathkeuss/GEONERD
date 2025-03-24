<?php

namespace App\Controller\Public\Authentication;

use App\Entity\User;
use App\Form\Public\SignUpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignUpController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signUp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher) {

        $user = new User();

        $formSignUp = $this->createForm(SignUpType::class, $user);

        $formSignUp->handleRequest($request);

        if($formSignUp->isSubmitted() && $formSignUp->isValid()) {

            $realPassword = $formSignUp->get('password')->getData();

            if(!$realPassword) {
                throw new \Exception('Le mot de passe est obligatoire');
            }

            $passwordHashed = $passwordHasher->hashPassword($user, $realPassword);
            $user->setPassword($passwordHashed);
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login_user');
        }

        $formSignUpView = $formSignUp->createView();

        return $this->render('public/authentication/signup.html.twig', [
            'formSignUpView' => $formSignUpView
        ]);
    }
}