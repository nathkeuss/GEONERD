<?php

namespace App\Controller\User;

use App\Entity\PostForum;
use App\Form\ForumPostType;
use App\Repository\PostForumRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserForumController extends AbstractController
{

    #[Route('/forum', name: 'forum')]
    public function listPosts(Request $request, PostForumRepository $postForumRepository)
    {
        $search = $request->query->get('search', '');

        if(!empty($search)) {
            $posts = $postForumRepository->findByTitleOrUsername($search);
        } else {
            $posts = $postForumRepository->findMainPosts();
        }

        return $this->render('/public/forum/post/list_posts.html.twig', [
            'posts' => $posts,
            'search' => $search
        ]);
    }

    #[Route('/forum/create-post', name: 'create_post')]
    public function createPost(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {

        $forumPost = new PostForum();

        $formForumPost = $this->createForm(ForumPostType::class, $forumPost);

        $formForumPost->handleRequest($request);

        if ($formForumPost->isSubmitted() && $formForumPost->isValid()) {

            $postImage = $formForumPost->get('image')->getData();

            if ($postImage) {

                $originalFilename = $postImage->getClientOriginalName();
                $fileExtension = $postImage->guessExtension();

                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($fileExtension, $originalFilename);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';

                $postImage->move($imgDir, $newFilename);

                $forumPost->setImage($newFilename);


            }

            $forumPost->setUser($this->getUser());
            $entityManager->persist($forumPost);
            $entityManager->flush();

            return $this->redirectToRoute('show_post', ['id' => $forumPost->getId()]);


        }

        $formForumPostView = $formForumPost->createView();

        return $this->render('/public/forum/post/create_post.html.twig', [
            'formForumPostView' => $formForumPostView,
        ]);
    }

    #[Route('/forum/{id}/update', name: 'update_post')]
    public function updatePost(int $id, Request $request, EntityManagerInterface $entityManager, PostForum $postForum, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        if($postForum->getUser() !== $this->getUser()){
            $this->addFlash('error', "Vous ne pouvez pas modifier ce message.");
            return $this->redirectToRoute('forum');
        }

        $formForumUpdatePost = $this->createForm(ForumPostType::class, $postForum);

        $formForumUpdatePost->handleRequest($request);

        if ($formForumUpdatePost->isSubmitted() && $formForumUpdatePost->isValid()) {

            $postImage = $formForumUpdatePost->get('image')->getData();

            if ($postImage) {

                $originalFilename = $postImage->getClientOriginalName();
                $fileExtension = $postImage->guessExtension();
                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($fileExtension, $originalFilename);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';

                $postImage->move($imgDir, $newFilename);

                if($postForum->getImage()) {
                    $oldFile = $imgDir . '/' . $postForum->getImage();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $postForum->setImage($newFilename);

            } else {
                $postForum->setImage($postForum->getImage());
            }

            $entityManager->persist($postForum);
            $entityManager->flush();

            return $this->redirectToRoute('show_post', ['id' => $postForum->getId()]);
        }

        $formForumUpdatePostView = $formForumUpdatePost->createView();

        return $this->render('public/forum/post/update_post.html.twig', [
            'formForumUpdatePostView' => $formForumUpdatePostView,
            'postForum' => $postForum,
        ]);

    }

    #[Route('/forum/{id}/delete', name: 'delete_post')]
    public function deletePost(int $id, EntityManagerInterface $entityManager, PostForum $postForum, ParameterBagInterface $parameterBagInterface)
    {
        if($postForum->getUser() !== $this->getUser()){
            $this->addFlash('error', "Vous ne pouvez pas supprimer ce message.");
        }

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
    public function replyPost(PostForum $parentPost, Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $reply = new PostForum();
        $reply->setParentPost($parentPost);
        $reply->setTitle('');

        $formReplyPost = $this->createForm(ForumPostType::class, $reply, [
            'is_reply' => true
        ]);

        $formReplyPost->handleRequest($request);

        if ($formReplyPost->isSubmitted() && $formReplyPost->isValid()) {

            $replyImage = $formReplyPost->get('image')->getData();

            if ($replyImage) {

                $originalFilename = $replyImage->getClientOriginalName();
                $fileExtension = $replyImage->guessExtension();

                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($fileExtension, $originalFilename);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';

                $replyImage->move($imgDir, $newFilename);

                $reply->setImage($newFilename);

            }

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
    public function updateReply(Request $request, EntityManagerInterface $entityManager, PostForum $reply, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        // Vérification de l'utilisateur autorisé à modifier la réponse
        if ($reply->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas modifier cette réponse.");
            return $this->redirectToRoute('show_post', ['id' => $reply->getParentPost()->getId()]);
        }

        // Mettre un titre vide pour les réponses
        $reply->setTitle('');

        // Création du formulaire
        $formUpdateReply = $this->createForm(ForumPostType::class, $reply, [
            'is_reply' => true,
        ]);

        $formUpdateReply->handleRequest($request);

        // Traitement du formulaire soumis
        if ($formUpdateReply->isSubmitted() && $formUpdateReply->isValid()) {
            // Gestion de l'image si elle est mise à jour
            $newImage = $formUpdateReply->get('image')->getData();
            if ($newImage) {
                $originalFilename = $newImage->getClientOriginalName();
                $fileExtension = $newImage->guessExtension();
                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($fileExtension, $originalFilename);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_forum';

                $newImage->move($imgDir, $newFilename);

                // Suppression de l'ancienne image si elle existe
                if ($reply->getImage()) {
                    $oldFile = $imgDir . '/' . $reply->getImage();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $reply->setImage($newFilename);

            } else {
                $reply->setImage($reply->getImage());
            }

            // Sauvegarde de la réponse mise à jour
            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash('success', "Votre réponse a été mise à jour avec succès !");

            return $this->redirectToRoute('show_post', ['id' => $reply->getParentPost()->getId()]);
        }

        // Préparer les données pour la vue
        $formUpdateReplyView = $formUpdateReply->createView();
        $parentPost = $reply->getParentPost();
        $replies = $parentPost->getReplies();

        // Rendu du formulaire de modification avec le contexte
        return $this->render('/public/forum/reply/update_reply.html.twig', [
            'formUpdateReplyView' => $formUpdateReplyView,
            'parentPost' => $parentPost,
            'replies' => $replies, // Ajout des réponses pour la boucle
            'updateReplyId' => $reply->getId(),
            'currentReply' => $reply
        ]);
    }



    #[Route('/forum/reply/{id}/delete', name: 'delete_reply')]
    public function deleteReply(EntityManagerInterface $entityManager, PostForum $reply, ParameterBagInterface $parameterBagInterface)
    {
        if ($reply->getUser() !== $this->getUser()) {
            $this->addFlash('error', "Vous ne pouvez pas supprimer cette réponse.");
            return $this->redirectToRoute('show_post', ['id' => $reply->getParentPost()->getId()]);
        }

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
        return $this->redirectToRoute('show_post', ['id' => $parentPostId]);
    }


}