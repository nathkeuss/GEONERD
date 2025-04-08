<?php

namespace App\Controller\Public\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginUserController extends AbstractController
{
    #[Route('/login', name: 'login_user', methods: ['GET', 'POST'])]
    public function userLogin(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('public/authentication/login_user.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout_user', methods: ['GET'])]
    public function userLogout() {
        # gérer par symfony grâce au security.yaml
    }

}