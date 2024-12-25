<?php

namespace App\Controller\User;

use App\Entity\PostForum;
use App\Form\ForumPostType;
use App\Repository\PostForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserForumController extends AbstractController
{

    #[Route('/forum', name: 'forum')]
    public function listPosts(Request $request, PostForumRepository $postForumRepository)
    {
        $posts = $postForumRepository->findMainPosts();

        return $this->render('/public/forum/post/list_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/forum/create-post', name: 'create_post')]
    public function createPost(Request $request, EntityManagerInterface $entityManager)
    {
        $forumPost = new PostForum();

        $formForumPost = $this->createForm(ForumPostType::class, $forumPost);

        $formForumPost->handleRequest($request);

        if ($formForumPost->isSubmitted() && $formForumPost->isValid()) {
            $forumPost->setUser($this->getUser());
            $entityManager->persist($forumPost);
            $entityManager->flush();

        }

        $formForumPostView = $formForumPost->createView();

        return $this->render('/public/forum/post/create_post.html.twig', [
            'formForumPostView' => $formForumPostView,
        ]);
    }

    #[Route('/forum/{id}/update', name: 'update_post')]
    public function updatePost(Request $request, EntityManagerInterface $entityManager, PostForum $postForum)
    {
        if($postForum->getUser() !== $this->getUser()){
            $this->addFlash('error', "Vous ne pouvez pas modifier ce message.");
        }

        $formForumUpdatePost = $this->createForm(ForumPostType::class, $postForum);

        $formForumUpdatePost->handleRequest($request);

        if ($formForumUpdatePost->isSubmitted() && $formForumUpdatePost->isValid()) {
            $entityManager->persist($postForum);
            $entityManager->flush();
        }

        $formForumUpdatePostView = $formForumUpdatePost->createView();

        return $this->render('public/forum/post/update_post.html.twig', [
            'formForumUpdatePostView' => $formForumUpdatePostView,
            'postForum' => $postForum
        ]);

    }

    #[Route('/forum/{id}/delete', name: 'delete_post')]
    public function deletePost(Request $request, EntityManagerInterface $entityManager, PostForum $postForum)
    {
        if($postForum->getUser() !== $this->getUser()){
            $this->addFlash('error', "Vous ne pouvez pas supprimer ce message.");
        }

        $entityManager->remove($postForum);
        $entityManager->flush();
        return $this->redirectToRoute('forum');
    }



    #[Route('/forum/{id}', name: 'show_post')]
    public function showPost(PostForum $post)
    {
        return $this->render('/public/forum/post/show_post.html.twig', [
            'post' => $post,
            'replies' => $post->getReplies()
        ]);
    }

    #[Route('/forum/{id}/reply', name: 'reply_post')]
    public function replyPost(PostForum $parentPost, Request $request, EntityManagerInterface $entityManager)
    {
        $reply = new PostForum();
        $reply->setParentPost($parentPost);
        $reply->setTitle('');

        $formReplyPost = $this->createForm(ForumPostType::class, $reply, [
            'is_reply' => true
        ]);

        $formReplyPost->handleRequest($request);

        if ($formReplyPost->isSubmitted() && $formReplyPost->isValid()) {
            $reply->setUser($this->getUser());
            $entityManager->persist($reply);
            $entityManager->flush();

            return $this->redirectToRoute('show_post', ['id' => $parentPost->getId()]);
        }

        $formReplyPostView = $formReplyPost->createView();

        return $this->render('/public/forum/reply/reply_post.html.twig', [
            'formReplyPostView' => $formReplyPostView,
            'parentPost' => $parentPost,
            'replies' => $parentPost->getReplies()
        ]);
    }


    #[Route('/forum/reply/{id}/update', name: 'update_reply')]
    public function updateReply(PostForum $parentPost, Request $request, EntityManagerInterface $entityManager, PostForum $reply)
    {
        if ($reply->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas modifier cette réponse.");
        }

        $reply->setTitle('');

        $formUpdateReply = $this->createForm(ForumPostType::class, $reply, [
            'is_reply' => true
        ]);

        $formUpdateReply->handleRequest($request);

        if ($formUpdateReply->isSubmitted() && $formUpdateReply->isValid()) {
            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash('success', "Votre message a bien été mis à jour !");
            return $this->redirectToRoute('show_post', ['id' => $reply->getParentPost()->getId()]);
        }

        $formUpdateReplyView = $formUpdateReply->createView();
        $parentPost = $reply->getParentPost();
        $replies = $parentPost->getReplies();

        return $this->render('/public/forum/reply/update_reply.html.twig', [
            'formUpdateReplyView' => $formUpdateReplyView,
            'parentPost' => $parentPost,
            'replies' => $parentPost->getReplies()
        ]);
    }


    #[Route('/forum/reply/{id}/delete', name: 'delete_reply')]
    public function deleteReply(EntityManagerInterface $entityManager, PostForum $reply)
    {
        if ($reply->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas supprimer cette réponse.");
        }

        $parentPostId = $reply->getParentPost()->getId();

        $entityManager->remove($reply);
        $entityManager->flush();

        $this->addFlash('success', "La réponse a été supprimée avec succès !");
        return $this->redirectToRoute('show_post', ['id' => $parentPostId]);
    }

}