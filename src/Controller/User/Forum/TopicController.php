<?php

namespace App\Controller\User\Forum;

use App\Entity\Topic;
use App\Form\User\Forum\TopicType;
use App\Repository\ReplyRepository;
use App\Repository\TopicRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    #[Route('/forum/topic/create', name: 'topic_create', methods: ['GET', 'POST'])]
    public function createTopic(Request                $request,
                                EntityManagerInterface $entityManager,
                                ImageUploader          $imageUploader)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login_user');
        }

        $topic = new Topic();

        $formTopic = $this->createForm(TopicType::class, $topic);

        $formTopic->handleRequest($request);

        if ($formTopic->isSubmitted() && $formTopic->isValid()) {

            $topicImage = $formTopic->get('image')->getData();

            if ($topicImage) {
                $imageNewFilename = $imageUploader->uploadImage($topicImage, 'forum/topics');
                $topic->setImage($imageNewFilename);
            }

            $topic->setUser($this->getUser());
            $entityManager->persist($topic);
            $entityManager->flush();

            $this->addFlash('success', 'Le topic a bien été créé');
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);

        }

        $formTopicView = $formTopic->createView();

        return $this->render('user/forum/topic/create.html.twig', [
            'formTopicView' => $formTopicView
        ]);

    }

    #[Route('/forum/topic/update/{id}', name: 'topic_update', methods: ['GET', 'POST'])]
    public function updateTopic(int                    $id,
                                Request                $request,
                                EntityManagerInterface $entityManager,
                                TopicRepository        $topicRepository,
                                ImageUploader          $imageUploader)
    {
        $topic = $topicRepository->find($id);

        if ($topic->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('topic_list');
        }

        $formTopic = $this->createForm(TopicType::class, $topic);

        $formTopic->handleRequest($request);

        if ($formTopic->isSubmitted() && $formTopic->isValid()) {

            $topicImage = $formTopic->get('image')->getData();

            if ($topicImage) {
                $imageUploader->removeImage('forum/topics', $topic->getImage());
                $imageNewFilename = $imageUploader->uploadImage($topicImage, 'forum/topics');
                $topic->setImage($imageNewFilename);
            }

            $entityManager->flush();
            $entityManager->persist($topic);

            $this->addFlash('success', 'Le topic a bien été modifié');
            return $this->redirectToRoute('topic_show', ['id' => $id]);

        }

        $formTopicView = $formTopic->createView();

        return $this->render('user/forum/topic/update.html.twig', [
            'formTopicView' => $formTopicView
        ]);

    }

    #[Route('/forum/topic/delete/{id}', name: 'topic_delete', methods: ['GET'])]
    public function deleteTopic(int                    $id,
                                EntityManagerInterface $entityManager,
                                TopicRepository        $topicRepository,
                                ReplyRepository        $replyRepository,
                                ImageUploader          $imageUploader)
    {
        $topic = $topicRepository->find($id);

        if ($topic->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('topic_list');
        }

        $replies = $replyRepository->findBy(['topic' => $topic]);
        foreach ($replies as $reply) {
            $imageUploader->removeImage('forum/replies', $reply->getImage());
            $entityManager->remove($reply);
        }

        $imageUploader->removeImage('forum/topics', $topic->getImage());

        $entityManager->remove($topic);
        $entityManager->flush();

        $this->addFlash('success', 'Le topic a bien été supprimé');
        return $this->redirectToRoute('topic_list');

    }

}