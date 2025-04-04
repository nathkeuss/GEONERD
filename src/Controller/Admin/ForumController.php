<?php

namespace App\Controller\Admin;

use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
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

}