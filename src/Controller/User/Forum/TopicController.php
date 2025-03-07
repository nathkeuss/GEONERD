<?php

namespace App\Controller\User\Forum;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    #[Route('/forum/topic/create', name: 'topic_create', methods: ['GET', 'POST'])]
    public function createTopic(Request                 $request,
                                EntityManagerInterface  $entityManager,
                                ImageUploader           $imageUploader)
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

            $entityManager->persist($topic);
            $entityManager->flush();

            $this->addFlash('success', 'Le topic a bien été créé');

        }

        $formTopicView = $formTopic->createView();

        return $this->render('user/forum/topic/create.html.twig', [
            'formTopicView' => $formTopicView
        ]);



    }

}