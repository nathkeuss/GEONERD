<?php

namespace App\Controller\User\Forum;

use App\Entity\Reply;
use App\Form\User\Forum\ReplyType;
use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
{
    #[Route('/forum/topic/{id}/reply/create', name: 'reply_create', methods: ['GET', 'POST'])]
    public function createReply(int                    $id,
                                Request                $request,
                                TopicRepository        $topicRepository,
                                EntityManagerInterface $entityManager,
                                ImageUploader          $imageUploader)
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('login_user');
        }

        $topic = $topicRepository->find($id);

        $reply = new Reply();
        $reply->setTopic($topic);

        $formReply = $this->createForm(ReplyType::class, $reply);

        $formReply->handleRequest($request);

        if ($formReply->isSubmitted() && $formReply->isValid()) {

            $replyImage = $formReply->get('image')->getData();

            if ($replyImage) {
                $imageNewFilename = $imageUploader->uploadImage($replyImage, 'forum/replies');
                $reply->setImage($imageNewFilename);
            }

            $reply->setUser($this->getUser());
            $entityManager->persist($reply);
            $entityManager->flush();

            $this->addFlash('success', 'La réponse a bien été ajoutée');
            return $this->redirectToRoute('topic_show', ['id' => $id]);
        }

        $formReplyView = $formReply->createView();

        return $this->render('user/forum/reply/create.html.twig', [
            'formReplyView' => $formReplyView
        ]);

    }

    #[Route('/forum/topic/{id}/reply/update/{replyId}', name: 'reply_update', methods: ['GET', 'POST'])]
    public function updateReply(int $id,
                                int $replyId,
                                Request $request,
                                TopicRepository $topicRepository,
                                ReplyRepository $replyRepository,
                                EntityManagerInterface $entityManager,
                                ImageUploader $imageUploader)
    {
        $topic = $topicRepository->find($id);
        $reply = $replyRepository->find($replyId);

        if ($reply->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('topic_show', ['id' => $id]);
        }

        $formReply = $this->createForm(ReplyType::class, $reply);

        $formReply->handleRequest($request);

        if ($formReply->isSubmitted() && $formReply->isValid()) {

            $replyImage = $formReply->get('image')->getData();

            if ($replyImage) {
                $imageUploader->removeImage('forum/replies', $reply->getImage());
                $imageNewFilename = $imageUploader->uploadImage($replyImage, 'forum/replies');
                $reply->setImage($imageNewFilename);
            }

            $entityManager->flush();
            $entityManager->persist($reply);

            $this->addFlash('success', 'La réponse a bien été modifiée');
            return $this->redirectToRoute('topic_show', ['id' => $id]);
        }

        $formReplyView = $formReply->createView();

        return $this->render('user/forum/reply/update.html.twig', [
            'formReplyView' => $formReplyView
        ]);

    }

    #[Route('/forum/topic/{id}/reply/delete/{replyId}', name: 'reply_delete', methods: ['GET'])]
    public function deleteReply(int $id,
                                int $replyId,
                                TopicRepository $topicRepository,
                                ReplyRepository $replyRepository,
                                EntityManagerInterface $entityManager,
                                ImageUploader $imageUploader)
    {
        $topic = $topicRepository->find($id);
        $reply = $replyRepository->find($replyId);

        if ($reply->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('topic_show', ['id' => $id]);
        }

        $imageUploader->removeImage('forum/replies', $reply->getImage());

        $entityManager->remove($reply);
        $entityManager->flush();

        $this->addFlash('success', 'La réponse a bien été supprimée');
        return $this->redirectToRoute('topic_show', ['id' => $id]);
    }

}