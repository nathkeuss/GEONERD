<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/logout', 'admin_logout', methods: ['GET'])]
    public function logout()
    {
        // cette route est utilisée par symfony
        // dans le security.yaml
        // pour gérer la deconnexion
    }

    public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {

    }

}