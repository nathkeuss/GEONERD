<?php

namespace App\Controller\Public\Tutorial;

use App\Repository\ContinentRepository;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ContinentController extends AbstractController
{
    #[Route('/continent/{slugContinent}', name: 'continent_show', methods: ['GET'], requirements: ['slugContinent' => '[a-zA-Z0-9-]+'])]
    public function showContinent(string              $slugContinent,
                                  ContinentRepository $continentRepository,
                                  CountryRepository   $countryRepository)
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $countries = $countryRepository->findBy(['continent' => $continent]);

        return $this->render('public/continent/show.html.twig', [
            'continent' => $continent,
            'countries' => $countries
        ]);

    }


}