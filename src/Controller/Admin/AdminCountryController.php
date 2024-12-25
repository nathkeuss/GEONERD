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

class AdminCountryController extends AbstractController
{
    #[Route('/admin/country/create', name: 'create_country')]
    public function createCountry(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $country = new Country();

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => false
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $countryFlagImage = $formCountry->get('flag')->getData();

            if ($countryFlagImage) {

                $imageOriginalName = $countryFlagImage->getClientOriginalName();
                $imageExtension = $countryFlagImage->guessExtension();

                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                $projectDir = $parameterBagInterface->get('kernel.project_dir');

                $imgDir = $projectDir . '/public/assets/img_uploads/img_flags';

                $countryFlagImage->move($imgDir, $imageNewFilename);

                $country->setFlag($imageNewFilename);
            }

            $entityManager->persist($country);
            $entityManager->flush();

            $this->addFlash('success', 'Pays bien ajouté!');
        }


        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/create_country.html.twig', [
            'formCountryView' => $formCountryView,
        ]);

    }

    #[Route('/admin/country/list', name: 'list_countries')]
    public function listCountries(CountryRepository $countryRepository, Request $request)
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $countries = $countryRepository->findByCountryName($search);
        } else {
            $countries = $countryRepository->findAll();
        }

        return $this->render('admin/country/list_countries.html.twig', [
            'countries' => $countries,
            'search' => $search,
        ]);


    }

    #[Route('/admin/country/{id}/update', name: 'update_country')]
    public function updateCountry(int $id, Request $request, EntityManagerInterface $entityManager, CountryRepository $countryRepository, ParameterBagInterface $parameterBagInterface, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $country = $countryRepository->find($id);

        $formCountry = $this->createForm(CountryType::class, $country , [
            'is_require' => true
        ]);
        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {

            $countryFlagImage = $formCountry->get('flag')->getData();

            if ($countryFlagImage) {
                // génère un nom unique pour la nouvelle image
                $originalFilename = $countryFlagImage->getClientOriginalName();
                $fileExtension = $countryFlagImage->guessExtension();
                $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($originalFilename, $fileExtension);

                // chemin du répertoire cible
                $projectDir = $parameterBagInterface->get('kernel.project_dir');
                $imgDir = $projectDir . '/public/assets/img_uploads/img_flags';

                // déplacer la nouvelle image dans le répertoire
                $countryFlagImage->move($imgDir, $newFilename);

                // supprimer l'ancien fichier si nécessaire
                if ($country->getFlag()) {
                    $oldFile = $imgDir . '/' . $country->getFlag();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // associer la nouvelle img au pays
                $country->setFlag($newFilename);
            } else {
                // aucun fichier soumis, conserve l'image actuelle
                $country->setFlag($country->getFlag());
            }


            $entityManager->persist($country);
            $entityManager->flush();

            return $this->redirectToRoute('list_countries');
            $this->addFlash('success', 'Pays bien modifié!');
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/update_country.html.twig', [
            'formCountryView' => $formCountryView,
            'currentFlag' => $country->getFlag(),
        ]);

    }

    #[Route('/admin/country/{id}/delete', name: 'delete_country')]
    public function deleteCountry(int $id, CountryRepository $countryRepository, EntityManagerInterface $entityManager)
    {
        $country = $countryRepository->find($id);

        $entityManager->remove($country);
        $entityManager->flush();

        $this->addFlash('success', 'Pays bien supprimé!');
        return $this->redirectToRoute('list_countries');
    }

}