<?php

namespace App\Controller\Admin;

use App\Entity\Tutorial;
use App\Form\TutorialType;
use App\Repository\ContinentRepository;
use App\Repository\CountryRepository;
use App\Repository\TutorialRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TutorialController extends AbstractController
{
    #[Route('/admin/continent/{slugContinent}/country/{slugCountry}/tutorial/create', name: 'tutorial_create', methods: ['GET', 'POST'])]
    public function createTutorial(string                 $slugContinent,
                                   string                 $slugCountry,
                                   Request                $request,
                                   EntityManagerInterface $entityManager,
                                   ImageUploader          $imageUploader,
                                   ContinentRepository    $continentRepository,
                                   CountryRepository      $countryRepository)
    {

        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);

        $tutorial = new Tutorial();
        $tutorial->setCountry($country);

        $formTutorial = $this->createForm(TutorialType::class, $tutorial, [
            'is_require' => false
        ]);

        $formTutorial->handleRequest($request);

        if ($formTutorial->isSubmitted() && $formTutorial->isValid()) {

            $tutorialImage = $formTutorial->get('image')->getData();
            $tutorialDescription = $formTutorial->get('description')->getData();

            if ($tutorialImage) {
                $imageNewFilename = $imageUploader->uploadImage($tutorialImage, 'country/tutorials');
                $tutorial->setImage($imageNewFilename);
            }

            $tutorial->setDescription($tutorialDescription);

            $entityManager->persist($tutorial);
            $entityManager->flush();

            $this->addFlash('success', 'Le tutoriel a bien été ajouté');
            return $this->redirectToRoute('country_show', [
                'slugContinent' => $slugContinent,
                'slug' => $slugCountry
            ]);
        }

        $formTutorialView = $formTutorial->createView();

        return $this->render('admin/tutorial/create.html.twig', [
            'formTutorialView' => $formTutorialView,
            'continent' => $continent,
            'country' => $country
        ]);

    }

    #[Route('/admin/continent/{slugContinent}/country/{slugCountry}/tutorial/update/{id}', name: 'tutorial_update', methods: ['GET', 'POST'])]
    public function updateTutorial(int                    $id,
                                   string                 $slugContinent,
                                   string                 $slugCountry,
                                   Request                $request,
                                   EntityManagerInterface $entityManager,
                                   ImageUploader          $imageUploader,
                                   ContinentRepository    $continentRepository,
                                   CountryRepository      $countryRepository,
                                   TutorialRepository     $tutorialRepository)
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);
        $tutorial = $tutorialRepository->find($id);

        $formTutorial = $this->createForm(TutorialType::class, $tutorial, [
            'is_require' => false
        ]);

        $formTutorial->handleRequest($request);

        if ($formTutorial->isSubmitted() && $formTutorial->isValid()) {
            $tutorialImage = $formTutorial->get('image')->getData();

            if($tutorialImage) {
                $imageUploader->removeImage('country/tutorials', $tutorial->getImage());
                $imageNewFilename = $imageUploader->uploadImage($tutorialImage, 'country/tutorials');
                $tutorial->setImage($imageNewFilename);
            }

            $entityManager->persist($tutorial);
            $entityManager->flush();

            $this->addFlash('success', 'Le tutoriel a bien été modifié');
            return $this->redirectToRoute('country_show', [
                'slugContinent' => $slugContinent,
                'slug' => $slugCountry
            ]);
        }

        $formTutorialView = $formTutorial->createView();

        return $this->render('admin/tutorial/update.html.twig', [
            'formTutorialView' => $formTutorialView,
            'currentImage' => $tutorial->getImage(),
            'continent' => $continent,
            'country' => $country,
            'tutorial' => $tutorial
        ]);

    }

}