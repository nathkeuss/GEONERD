<?php

namespace App\Controller\User\Forum;

use App\Entity\Reply;
use App\Form\ReplyType;
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

}