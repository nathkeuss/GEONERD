<?php

namespace App\Controller\Public\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginUserController extends AbstractController
{
    #[Route('/login', name: 'login_user', methods: ['GET', 'POST'])]
    public function userLogin() {
        return $this->render('public/authentication/login_user.html.twig');
    }

    #[Route('/logout', name: 'logout_user', methods: ['GET'])]
    public function userLogout() {
        # gérer par symfony grâce au security.yaml
    }

}