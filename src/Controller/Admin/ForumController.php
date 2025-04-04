<?php

namespace App\Controller\Admin;

use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    #[Route('/admin/forum/topic/list', name: 'admin_topic_list')]
    public function listTopics(TopicRepository $topicRepository,
                               Request         $request): Response
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $topics = $topicRepository->searchByTopicTitle($search);
        } else {
            $topics = $topicRepository->findBy([], ['date' => 'DESC']);
        }

        return $this->render('admin/forum/topic/list.html.twig', [
            'topics' => $topics,
            'search' => $search
        ]);
    }

    #[Route('/admin/forum/topic/show/{id}', name: 'admin_topic_show', methods: ['GET'])]
    public function showTopic(int             $id,
                              TopicRepository $topicRepository,
                              ReplyRepository $replyRepository): Response
    {
        $topic = $topicRepository->find($id);
        $replies = $replyRepository->findBy(['topic' => $topic]);

        return $this->render('admin/forum/topic/show.html.twig', [
            'topic' => $topic,
            'replies' => $replies
        ]);
    }

    #[Route('/admin/forum/topic/delete/{id}', name: 'admin_topic_delete', methods: ['GET'])]
    public function deleteTopic(int             $id,
                                EntityManagerInterface $entityManager,
                                ImageUploader $imageUploader,
                                TopicRepository $topicRepository,
                                ReplyRepository $replyRepository): Response
    {
        $topic = $topicRepository->find($id);

        $replies = $replyRepository->findBy(['topic' => $topic]);
        foreach ($replies as $reply) {
            $imageUploader->removeImage('forum/replies', $reply->getImage());
            $entityManager->remove($reply);
        }

        $imageUploader->removeImage('forum/topics', $topic->getImage());

        $entityManager->remove($topic);
        $entityManager->flush();

        $this->addFlash('success', 'Le topic a bien été supprimé');
        return $this->redirectToRoute('admin_topic_list');

    }

    #[Route('/admin/forum/topic/{id}/reply/delete/{replyId}', name: 'admin_reply_delete', methods: ['GET'])]
    public function deleteReply(int $id,
                                int $replyId,
                                TopicRepository $topicRepository,
                                ReplyRepository $replyRepository,
                                EntityManagerInterface $entityManager,
                                ImageUploader $imageUploader)
    {
        $topic = $topicRepository->find($id);
        $reply = $replyRepository->find($replyId);


        $imageUploader->removeImage('forum/replies', $reply->getImage());

        $entityManager->remove($reply);
        $entityManager->flush();

        $this->addFlash('success', 'La réponse a bien été supprimée');
        return $this->redirectToRoute('admin_topic_show', ['id' => $id]);
    }

}