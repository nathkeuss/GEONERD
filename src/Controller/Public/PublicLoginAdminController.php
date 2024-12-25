<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PublicLoginAdminController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_login')]
    public function adminLogin(AuthenticationUtils $authenticationUtils)
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('public/login/login_admin.html.twig', [
            'error' => $error,
        ]);

    }

}