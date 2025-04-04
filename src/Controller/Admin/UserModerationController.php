<?php

namespace App\Controller\Admin;

use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserModerationController extends AbstractController
{
    #[Route('admin/user/{id}', name: 'admin_show_user', methods: ['GET'])]
    public function showUser(int             $id,
                             UserRepository  $userRepository,
                             ReplyRepository $replyRepository,
                             TopicRepository $topicRepository)
    {
        $user = $userRepository->find($id);

        $topics = $topicRepository->findBy(['user' => $id]);
        $replies = $replyRepository->findBy(['user' => $id]);

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'topics' => $topics,
            'replies' => $replies,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST', 'DELETE', 'GET'])]
    public function deleteUser(int $id,
                               UserRepository $userRepository,
                               EntityManagerInterface $entityManager,
                               ImageUploader $imageUploader)
    {
        $user = $userRepository->find($id);

        $user->setIsDeleted(true);
        $entityManager->flush();

        $imageUploader->removeImage('user/profile_pictures', $user->getProfilePicture());

        $this->addFlash('success', "L'utilisateur a bien été supprimé (soft delete)");

        return $this->redirectToRoute('dashboard_admin');
    }



}