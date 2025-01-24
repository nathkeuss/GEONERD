<?php

namespace App\Controller\Admin;

use App\Entity\PostForum;
use App\Repository\PostForumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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

    #[Route('/admin/show-user/{id}', 'admin_show_user', methods: ['GET'])]
    public function showUser(int $id, UserRepository $userRepository, PostForumRepository $postForumRepository) {

        $user = $userRepository->find($id);
        $posts = $postForumRepository->findBy(['user' => $user]);

        return $this->render('admin/user/show_user.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    #[Route('admin/show-user/{id}/delete', name: 'admin_delete_user_post')]
    public function deleteUserReply(EntityManagerInterface $entityManager, PostForum $reply, ParameterBagInterface $parameterBagInterface)
    {

        // Suppression de l'image associée à la réponse (si elle existe)
        if ($reply->getImage()) {
            $projectDir = $parameterBagInterface->get('kernel.project_dir');
            $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';
            $imagePath = $imgDir . '/' . $reply->getImage();

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Récupération de l'ID du post parent pour redirection après suppression
        $parentPostId = $reply->getParentPost()->getId();

        // Suppression de la réponse
        $entityManager->remove($reply);
        $entityManager->flush();

        $this->addFlash('success', "La réponse a été supprimée avec succès !");
        return $this->redirectToRoute('admin_dashboard');
    }


    #[Route('/admin/user/{id}', name: 'admin_delete_user')]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager) {

        $user = $userRepository->find($id);

        if($user) {
            foreach($user->getReplies() as $post) {
                $entityManager->remove($post);
            }
/*
            $profilePicture = $user->getProfilePicture();
            if ($profilePicture) {
                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_profile/';
                $filePath = $imgDir . $profilePicture;

                // Vérifier si le fichier existe et le supprimer
                if (file_exists($filePath)) {
                    unlink($filePath); // Supprimer le fichier de l'image
                }
            }*/
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard');

    }


}