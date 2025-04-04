<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'dashboard_admin')]
    public function dashboard(UserRepository $userRepository)
    {
        $users = $userRepository->findBy(['isDeleted' => false]);

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

}