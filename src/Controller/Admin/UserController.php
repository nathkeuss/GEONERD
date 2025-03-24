<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('admin/logout', name: 'logout_admin', methods: ['GET'])]
    public function adminLogout() {
        # gérer par symfony grâce au security.yaml
    }

}