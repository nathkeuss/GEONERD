<?php

namespace App\Controller\Admin;

use App\Entity\TutorialPart;
use App\Form\TutorialPartType;
use App\Repository\TutorialPartRepository;
use App\Repository\TutorialRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminTutorialPartController extends AbstractController
{
    #[Route('/admin/tutorialpart/create', name: 'create_tutorialpart')]
    public function createTutorialPart(Request $request, EntityManagerInterface $entityManager, UniqueFilenameGenerator $uniqueFilenameGenerator, ParameterBagInterface $parameterBagInterface)
    {
        $tutorialPart = new TutorialPart();

        $formTutorialPart = $this->createForm(TutorialPartType::class, $tutorialPart, [
            'is_require' => false
        ]);

        $formTutorialPart->handleRequest($request);

        if ($formTutorialPart->isSubmitted() && $formTutorialPart->isValid()) {

            $tutorialPartImage = $formTutorialPart->get('image')->getData();

            if ($tutorialPartImage) {
                $imageOriginalName = $tutorialPartImage->getClientOriginalName();
                $imageExtension = $tutorialPartImage->guessExtension();

                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img_uploads/img_tutorials';

                $tutorialPartImage->move($imgDir, $imageNewFilename);

                $tutorialPart->setImage($imageNewFilename);
            }
            $entityManager->persist($tutorialPart);
            $entityManager->flush();

            $this->addFlash('success', 'Partie du tutoriel bien ajoutée!');
            return $this->redirectToRoute('list_tutorials');
        }

        $formTutorialPartView = $formTutorialPart->createView();

        return $this->render('admin/tutorialpart/create_tutorial_part.html.twig', [
            'formTutorialPartView' => $formTutorialPartView,
        ]);

    }

    #[Route('/admin/tutorialpart/{id}/update', name: 'update_tutorial_part')]
    public function updateTutorialPart(int $id, TutorialPartRepository $tutorialPartRepository,Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface,UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $tutorialPart = $tutorialPartRepository->find($id);

        $formTutorialPart = $this->createForm(TutorialPartType::class, $tutorialPart, [
            'is_require' => true
        ]);

        $formTutorialPart->handleRequest($request);

        if ($formTutorialPart->isSubmitted() && $formTutorialPart->isValid()) {

            $tutorialPartImage = $formTutorialPart->get('image')->getData();

            if ($tutorialPartImage) {
                // génère un nom unique pour la nouvelle image
                $originalFilename = $tutorialPartImage->getClientOriginalName();
                $fileExtension = $tutorialPartImage->guessExtension();
                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($originalFilename, $fileExtension);

                // chemin du répertoire cible
                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_tutorials';

                // déplacer la nouvelle image dans le répertoire
                $tutorialPartImage->move($imgDir, $newFilename);

                // supprimer l'ancien fichier si nécessaire
                if ($tutorialPart->getImage()) {
                    $oldFile = $imgDir . '/' . $tutorialPart->getImage();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // associer la nouvelle img du tuto
                $tutorialPart->setImage($newFilename);
            } else {
                // aucun fichier soumis, conserve l'image actuelle
                $tutorialPart->setImage($tutorialPart->getImage());
            }


            $entityManager->persist($tutorialPart);
            $entityManager->flush();

            return $this->redirectToRoute('list_tutorials');
            $this->addFlash('success', 'Partie du tutoriel bien modifiée!');
        }

        $formTutorialPartView = $formTutorialPart->createView();

        return $this->render('admin/tutorialpart/update_tutorial_part.html.twig', [
            'formTutorialPartView' => $formTutorialPartView,
            'currentImage' => $tutorialPart->getImage(),
            'tutorialPart' => $tutorialPart,
        ]);

    }

    #[Route('/admin/tutorialpart/{id}/delete', name: 'delete_tutorial_part')]
    public function deleteTutorialPart(int $id, TutorialPartRepository $tutorialPartRepository, EntityManagerInterface $entityManager)
    {
        $tutorialPart = $tutorialPartRepository->find($id);

        $entityManager->remove($tutorialPart);
        $entityManager->flush();

        return $this->redirectToRoute('list_tutorials');
    }
}