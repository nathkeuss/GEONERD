<?php

namespace App\Controller\Admin;

use App\Entity\PostForum;
use App\Repository\PostForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminForumController extends AbstractController
{

    #[Route('/admin/forum', name: 'admin_forum')]
    public function listPosts(Request $request, PostForumRepository $postForumRepository)
    {
        $posts = $postForumRepository->findMainPosts();

        return $this->render('/admin/forum/list_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/admin/forum/{id}', name: 'admin_show_post')]
    public function showPost(PostForum $post)
    {
        return $this->render('/admin/forum/show_post.html.twig', [
            'post' => $post,
            'replies' => $post->getReplies()
        ]);
    }

    #[Route('admin/forum/{id}/delete', name: 'admin_delete_post')]
    public function deletePost(int $id, EntityManagerInterface $entityManager, PostForum $postForum, ParameterBagInterface $parameterBagInterface)
    {

        if ($postForum->getImage()) {
            $projectDir = $parameterBagInterface->get('kernel.project_dir');
            $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';
            $imagePath = $imgDir . '/' . $postForum->getImage();

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($postForum);
        $entityManager->flush();
        return $this->redirectToRoute('admin_forum');
    }

    #[Route('admin/forum/reply/{id}/delete', name: 'admin_delete_reply')]
    public function deleteReply(EntityManagerInterface $entityManager, PostForum $reply, ParameterBagInterface $parameterBagInterface)
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
        return $this->redirectToRoute('admin_show_post', ['id' => $parentPostId]);
    }

}