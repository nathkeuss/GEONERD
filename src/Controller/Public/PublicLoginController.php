<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PublicLoginController extends AbstractController
{

    #[Route('login', name: 'public_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('public/login/login.html.twig', [
            'error' => $error,
        ]);

    }

    #[Route('/logout', name: 'user_logout', methods: ['GET'])]
    public function logout()
    {
        return $this->redirectToRoute('home');
    }

}