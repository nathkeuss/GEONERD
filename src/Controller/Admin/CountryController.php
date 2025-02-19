<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\CountryType;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    #[Route('/admin/country/create', name: 'country_create')]
    public function createCountry(Request $request,
                                  EntityManagerInterface $entityManager,
                                  ParameterBagInterface $parameterBag,
                                  UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $country = new Country();

        $formCountry = $this->createForm(CountryType::class, $country, [
            'is_require' => false
        ]);

        $formCountry->handleRequest($request);

        if ($formCountry->isSubmitted() && $formCountry->isValid()) {
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
            return $this->redirectToRoute('dashboard_admin');
        }

        $formCountryView = $formCountry->createView();

        return $this->render('admin/country/create.html.twig', [
            'formCountryView' => $formCountryView
        ]);
    }

}