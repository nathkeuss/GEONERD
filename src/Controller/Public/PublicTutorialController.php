<?php

namespace App\Controller\Public;

use App\Repository\TutorialPartRepository;
use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PublicTutorialController extends AbstractController
{
    #[Route('/tutorial/list', name: 'public_list_tutorials')]
    public function listTutorials(TutorialRepository $tutorialRepository, Request $request)
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $tutorials = $tutorialRepository->findByTutorialTitle($search);
        } else {
            $tutorials = $tutorialRepository->findAll();
        }

        return $this->render('public/tutorial/list_tutorials.html.twig', [
            'tutorials' => $tutorials,
            'search' => $search,
        ]);

    }

    #[Route('/tutorial/{id}', name: 'public_show_tutorial', methods: ['GET'])]
    public function showTutorial(int $id, TutorialRepository $tutorialRepository, TutorialPartRepository $tutorialPartRepository)
    {
        $tutorialParts = $tutorialPartRepository->findBy(['tutorial' => $tutorialRepository->find($id)]);
        $tutorial = $tutorialRepository->find($id);

        return $this->render('public/tutorial/show_tutorial.html.twig', [
            'tutorial' => $tutorial,
            'tutorialParts' => $tutorialParts,
        ]);
    }

}