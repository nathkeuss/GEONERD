<?php

namespace App\Controller\Public;

use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    #[Route('/country/list', name: 'country_list', methods: ['GET'])]
    public function listCountries(CountryRepository $countryRepository)
    {
        $countries = $countryRepository->findAll();

        return $this->render('public/country/list.html.twig', [
            'countries' => $countries
        ]);

    }

}