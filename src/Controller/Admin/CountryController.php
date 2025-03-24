<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\Admin\CountryType;
use App\Repository\ContinentRepository;
use App\Repository\CountryRepository;
use App\Repository\TutorialRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CountryController extends AbstractController
{
    #[Route('/admin/continent/{slugContinent}/country/create', name: 'country_create', methods: ['GET', 'POST'])]
    public function createCountry(string                 $slugContinent,
                                  Request                $request,
                                  EntityManagerInterface $entityManager,
                                  ImageUploader          $imageUploader,
                                  SluggerInterface       $slugger,
                                  ContinentRepository    $continentRepository)
    {

        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);

        $country = new Country();
        $country->setContinent($continent);

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => false
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $country->setSlugFromName($slugger);

            $countryFlag = $formCountry->get('flag')->getData();
            $countryBanner = $formCountry->get('banner')->getData();

            if ($countryFlag) {
                $imageNewFilename = $imageUploader->uploadImage($countryFlag, 'country/flags');
                $country->setFlag($imageNewFilename);
            }

            if ($countryBanner) {
                $imageNewFilename = $imageUploader->uploadImage($countryBanner, 'country/banners');
                $country->setBanner($imageNewFilename);
            }

            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Le pays a bien été ajouté');
            return $this->redirectToRoute('continent_show', ['slug' => $slugContinent]);
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/create.html.twig', [
            'formCountryView' => $formCountryView,
            'continent' => $continent

        ]);
    }

    #[Route('/admin/continent/{slugContinent}/country/show/{slugCountry}', name: 'country_show', methods: ['GET'])]
    public function showCountry(string              $slugContinent,
                                string              $slugCountry,
                                ContinentRepository $continentRepository,
                                CountryRepository   $countryRepository,
                                TutorialRepository  $tutorialRepository)
    {

        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);
        $tutorials = $tutorialRepository->findBy(['country' => $country]);

        return $this->render('admin/country/show.html.twig', [
            'country' => $country,
            'continent' => $continent,
            'tutorials' => $tutorials
        ]);
    }

    #[Route('/admin/continent/{slugContinent}/country/update/{slugCountry}', name: 'country_update', methods: ['GET', 'POST'])]
    public function updateCountry(string                 $slugContinent,
                                  string                 $slugCountry,
                                  Request                $request,
                                  EntityManagerInterface $entityManager,
                                  CountryRepository      $countryRepository,
                                  ContinentRepository    $continentRepository,
                                  ImageUploader          $imageUploader,
                                  SluggerInterface       $slugger
    )
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => true
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $country->setSlugFromName($slugger);
            $country->setContinent($continent);

            $countryFlag = $formCountry->get('flag')->getData();
            $countryBanner = $formCountry->get('banner')->getData();

            if ($countryFlag) {
                $imageUploader->removeImage('country/flags', $country->getFlag());
                $newFilename = $imageUploader->uploadImage($countryFlag, 'country/flags');
                $country->setFlag($newFilename);
            }

            if ($countryBanner) {
                $imageUploader->removeImage('country/banners', $country->getBanner());
                $newFilename = $imageUploader->uploadImage($countryBanner, 'country/banners');
                $country->setBanner($newFilename);
            }


            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Le pays a bien été modifié');
            return $this->redirectToRoute('continent_show', ['slug' => $slugContinent]);
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/update.html.twig', [
            'formCountryView' => $formCountryView,
            'currentFlag' => $country->getFlag(),
            'currentBanner' => $country->getBanner(),
            'country' => $country,
            'continent' => $continent
        ]);

    }

    #[Route('/admin/continent/{slugContinent}/country/delete/{slugCountry}', name: 'country_delete', methods: ['GET', 'POST'])]
    public function deleteCountry(string                 $slugContinent,
                                  string                 $slugCountry,
                                  EntityManagerInterface $entityManager,
                                  ContinentRepository    $continentRepository,
                                  CountryRepository      $countryRepository,
                                  ImageUploader          $imageUploader)
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);

        $imageUploader->removeImage('country/flags', $country->getFlag());
        $imageUploader->removeImage('country/banners', $country->getBanner());

        $entityManager->remove($country);
        $entityManager->flush();

        $this->addFlash('success', 'Le pays a bien été supprimé');
        return $this->redirectToRoute('continent_show', ['slug' => $slugContinent]);
    }

}