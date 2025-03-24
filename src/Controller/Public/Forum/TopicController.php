<?php

namespace App\Controller\Public\Forum;

use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    #[Route('/forum/topic/list', name: 'topic_list', methods: ['GET'])]
    public function listTopics(TopicRepository $topicRepository,
                               Request         $request)
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $topics = $topicRepository->searchByTopicTitle($search);
        } else {
            $topics = $topicRepository->findAll();
        }

        return $this->render('public/forum/topic/list.html.twig', [
            'topics' => $topics,
            'search' => $search
        ]);

    }

    #[Route('/forum/topic/show/{id}', name: 'topic_show', methods: ['GET'])]
    public function showTopic(int             $id,
                              TopicRepository $topicRepository,
                              ReplyRepository $replyRepository)
    {
        $topic = $topicRepository->find($id);
        $replies = $replyRepository->findBy(['topic' => $topic]);

        return $this->render('public/forum/topic/show.html.twig', [
            'topic' => $topic,
            'replies' => $replies
        ]);

    }

}