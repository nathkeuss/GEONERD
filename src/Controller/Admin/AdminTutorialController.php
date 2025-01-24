<?php

namespace App\Controller\Admin;

use App\Entity\Tutorial;
use App\Form\TutorialType;
use App\Repository\TutorialPartRepository;
use App\Repository\TutorialRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminTutorialController extends AbstractController
{
    #[Route('/admin/tutorial/create', name: 'create_tutorial')]
    public function createTutorial(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $tutorial = new Tutorial();

        $formTutorial = $this->createForm(TutorialType::class, $tutorial, [
            'is_require' => false
        ]);

        $formTutorial->handleRequest($request);

        if ($formTutorial->isSubmitted() && $formTutorial->isValid()) {


            $projectDir = $parameterBagInterface->get('kernel.project_dir');
            $imgDir = $projectDir . '/public/assets/img_uploads/img_tutorials';

            foreach (['image', 'backgroundImage'] as $field) {
                $file = $formTutorial->get($field)->getData();
                if ($file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->guessExtension();
                    $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($originalName, $extension);

                    $file->move($imgDir, $newFilename);

                    if ($field === 'image') {
                        $tutorial->setImage($newFilename);
                    } elseif ($field === 'backgroundImage') {
                        $tutorial->setBackgroundImage($newFilename);
                    }
                }
            }


            $entityManager->persist($tutorial);
            $entityManager->flush();

            $this->addFlash('success', 'Tutoriel bien ajouté!');
            return $this->redirectToRoute('list_tutorials');
        }

        $formTutorialView = $formTutorial->createView();

        return $this->render('admin/tutorial/create_tutorial.html.twig', [
            'formTutorialView' => $formTutorialView,
        ]);
    }

    #[Route('/admin/tutorial/list', name: 'list_tutorials')]
    public function listTutorials(TutorialRepository $tutorialRepository, Request $request)
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $tutorials = $tutorialRepository->findByTutorialTitle($search);
        } else {
            $tutorials = $tutorialRepository->findAll();
        }

        return $this->render('admin/tutorial/list_tutorials.html.twig', [
            'tutorials' => $tutorials,
            'search' => $search,
        ]);
    }

    #[Route('/admin/tutorial/{id}/update', name: 'update_tutorial')]
    public function updateTutorial(int $id, Request $request, EntityManagerInterface $entityManager, TutorialRepository $tutorialRepository, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $tutorial = $tutorialRepository->find($id);

        $formTutorial = $this->createForm(TutorialType::class, $tutorial, [
            'is_require' => true
        ]);

        $formTutorial->handleRequest($request);

        if ($formTutorial->isSubmitted() && $formTutorial->isValid()) {


            $projectDir = $parameterBagInterface->get('kernel.project_dir');
            $imgDir = $projectDir . '/public/assets/img_uploads/img_tutorials';

            foreach (['image', 'backgroundImage'] as $field) {
                $file = $formTutorial->get($field)->getData();
                if ($file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->guessExtension();
                    $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($originalName, $extension);

                    $file->move($imgDir, $newFilename);

                    if ($field === 'image' && $tutorial->getImage()) {
                        $oldFile = $imgDir . '/' . $tutorial->getImage();
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                        $tutorial->setImage($newFilename);
                    } elseif ($field === 'backgroundImage' && $tutorial->getBackgroundImage()) {
                        $oldFile = $imgDir . '/' . $tutorial->getBackgroundImage();
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                        $tutorial->setBackgroundImage($newFilename);
                    }
                }
            }



            $entityManager->persist($tutorial);
            $entityManager->flush();

            return $this->redirectToRoute('list_tutorials');
            $this->addFlash('success', 'Tutoriel bien modifié!');
            return $this->redirectToRoute('list_tutorials');
        }

        $formTutorialView = $formTutorial->createView();

        return $this->render('admin/tutorial/update_tutorial.html.twig', [
            'formTutorialView' => $formTutorialView,
            'currentImage' => $tutorial->getImage(),
            'currentBackgroundImage' => $tutorial->getBackgroundImage(),
            'tutorial' => $tutorial,
        ]);
    }

    #[Route('/admin/tutorial/{id}/delete', name: 'delete_tutorial')]
    public function deleteTutorial(int $id, TutorialRepository $tutorialRepository, EntityManagerInterface $entityManager)
    {
        $tutorial = $tutorialRepository->find($id);

        $entityManager->remove($tutorial);
        $entityManager->flush();

        $this->addFlash('success', 'Tutoriel bien supprimé');
        return $this->redirectToRoute('list_tutorials');
    }

    #[Route('/admin/tutorial/{id}', name: 'show_tutorial', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showTutorial(int $id, TutorialRepository $tutorialRepository, TutorialPartRepository $tutorialPartRepository)
    {
        $tutorialParts = $tutorialPartRepository->findBy(['tutorial' => $tutorialRepository->find($id)]);
        $tutorial = $tutorialRepository->find($id);

        return $this->render('admin/tutorial/show_tutorial.html.twig', [
            'tutorial' => $tutorial,
            'tutorialParts' => $tutorialParts,
        ]);
    }

}