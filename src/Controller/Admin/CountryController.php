<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CountryController extends AbstractController
{
    #[Route('/admin/country/create', name: 'country_create', methods: ['GET', 'POST'])]
    public function createCountry(Request                 $request,
                                  EntityManagerInterface  $entityManager,
                                  ParameterBagInterface   $parameterBag,
                                  UniqueFilenameGenerator $uniqueFilenameGenerator,
                                  SluggerInterface        $slugger)
    {
        $country = new Country();

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => false
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $country->setSlugFromName($slugger);

            $countryFlag = $formCountry->get('flag')->getData();
            $countryBanner = $formCountry->get('banner')->getData();

            if ($countryFlag) {
                $imageOriginalName = $countryFlag->getClientOriginalName();
                $imageExtension = $countryFlag->guessExtension();

                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBag->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img/uploads/country/flags';

                $countryFlag->move($imgDir, $imageNewFilename);

                $country->setFlag($imageNewFilename);
            }

            if ($countryBanner) {
                $imageOriginalName = $countryBanner->getClientOriginalName();
                $imageExtension = $countryBanner->guessExtension();

                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBag->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img/uploads/country/banners';

                $countryBanner->move($imgDir, $imageNewFilename);

                $country->setBanner($imageNewFilename);
            }

            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Le pays a bien été ajouté');
            return $this->redirectToRoute('admin_country_list');
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/create.html.twig', [
            'formCountryView' => $formCountryView
        ]);
    }

    #[Route('/admin/country/list', name: 'admin_country_list', methods: ['GET'])]
    public function listCountries(Request $request, CountryRepository $countryRepository)
    {
        $countries = $countryRepository->findAll();

        return $this->render('admin/country/list.html.twig', [
            'countries' => $countries
        ]);
    }

    #[Route('/admin/country/show/{slug}', name: 'country_show', methods: ['GET'])]
    public function showCountry(string $slug, CountryRepository $countryRepository)
    {
        $country = $countryRepository->findOneBy(['slug' => $slug]);

        return $this->render('admin/country/show.html.twig', [
            'country' => $country
        ]);
    }

    #[Route('/admin/country/update/{slug}', name: 'country_update', methods: ['GET', 'POST'])]
    public function updateCountry(string                     $slug,
                                  Request                 $request,
                                  EntityManagerInterface  $entityManager,
                                  CountryRepository       $countryRepository,
                                  UniqueFilenameGenerator $uniqueFilenameGenerator,
                                  ParameterBagInterface   $parameterBag,
                                  SluggerInterface        $slugger
    )
    {
        $country = $countryRepository->findOneBy(['slug' => $slug]);

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => true
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $country->setSlugFromName($slugger);

            $countryFlag = $formCountry->get('flag')->getData();
            $countryBanner = $formCountry->get('banner')->getData();

            if ($countryFlag) {
                $imageOriginalName = $countryFlag->getClientOriginalName();
                $imageExtension = $countryFlag->guessExtension();
                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBag->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img/uploads/country/flags';

                $countryFlag->move($imgDir, $imageNewFilename);

                if ($country->getFlag()) {
                    $oldFile = $imgDir . '/' . $country->getFlag();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $country->setFlag($imageNewFilename);
            } else {
                $country->setFlag($country->getFlag());
            }

            if ($countryBanner) {
                $imageOriginalName = $countryBanner->getClientOriginalName();
                $imageExtension = $countryBanner->guessExtension();
                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBag->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img/uploads/country/banners';

                $countryBanner->move($imgDir, $imageNewFilename);

                if ($country->getBanner()) {
                    $oldFile = $imgDir . '/' . $country->getBanner();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $country->setBanner($imageNewFilename);
            } else {
                $country->setBanner($country->getBanner());
            }

            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Le pays a bien été modifié');
            return $this->redirectToRoute('admin_country_list');
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/update.html.twig', [
            'formCountryView' => $formCountryView,
            'currentFlag' => $country->getFlag(),
            'currentBanner' => $country->getBanner(),
            'country' => $country
        ]);

    }

    #[Route('/admin/country/delete/{slug}', name: 'country_delete', methods: ['GET', 'POST'])]
    public function deleteCountry(string $slug,
                                  EntityManagerInterface $entityManager,
                                  CountryRepository $countryRepository,
                                  SluggerInterface $slugger,
                                  ParameterBagInterface $parameterBag)
    {
        $country = $countryRepository->findOneBy(['slug' => $slug]);

        $projectDir = $parameterBag->get('kernel.project_dir');

        $imgDirFlag = $projectDir . '/public/assets/img/uploads/country/flags';
        $imgDirBanner = $projectDir . '/public/assets/img/uploads/country/banners';

        if ($country->getFlag()) {
            $oldFileFlag = $imgDirFlag . '/' . $country->getFlag();
            if (file_exists($oldFileFlag)) {
                unlink($oldFileFlag);
            }
        }

        if ($country->getBanner()) {
            $oldFileBanner = $imgDirBanner . '/' . $country->getBanner();
            if (file_exists($oldFileBanner)) {
                unlink($oldFileBanner);
            }
        }

        $entityManager->remove($country);
        $entityManager->flush();

        $this->addFlash('success', 'Le pays a bien été supprimé');
        return $this->redirectToRoute('admin_country_list');
    }

}