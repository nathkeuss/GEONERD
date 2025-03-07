<?php

namespace App\Controller\Public;

use App\Repository\ContinentRepository;
use App\Repository\CountryRepository;
use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    #[Route('/country/list', name: 'country_list', methods: ['GET'])]
    public function listCountriesAndContinent(CountryRepository   $countryRepository,
                                              ContinentRepository $continentRepository)
    {
        $countries = $countryRepository->findAll();
        $continents = $continentRepository->findAll();

        return $this->render('public/country/list.html.twig', [
            'countries' => $countries,
            'continents' => $continents
        ]);
    }

    #[Route('/continent/{slugContinent}/country/{slugCountry}', name: 'country_show', methods: ['GET'])]
    public function showCountry(string             $slugCountry,
                                CountryRepository  $countryRepository,
                                TutorialRepository $tutorialRepository)
    {
        $country = $countryRepository->findOneBy(['slug' => $slugCountry]);
        $tutorials = $tutorialRepository->findBy(['country' => $country]);

        return $this->render('public/country/show.html.twig', [
            'country' => $country,
            'tutorials' => $tutorials
        ]);
    }

}