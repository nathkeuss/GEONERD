<?php

namespace App\Controller\Public\Authentication;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginAdminController extends AbstractController
{
    #[Route('/admin/login', name: 'login_admin', methods: ['GET', 'POST'])]
    public function adminLogin() {
        return $this->render('public/authentication/login_admin.html.twig');
    }

}