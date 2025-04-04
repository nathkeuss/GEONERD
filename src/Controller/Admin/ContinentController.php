<?php

namespace App\Controller\Admin;

use App\Entity\Continent;
use App\Form\Admin\ContinentType;
use App\Repository\ContinentRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ContinentController extends AbstractController
{
    #[Route('/admin/continent/create', name: 'continent_create', methods: ['GET', 'POST'])]
    public function createContinent(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $continent = new Continent();

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {

            $continent->setSlugFromName($slugger);

            $entityManager->persist($continent);
            $entityManager->flush();

            $this->addFlash('success', 'Continent bien ajouté!');
            return $this->redirectToRoute('admin_continent_list');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/create.html.twig', [
            'formContinentView' => $formContinentView
        ]);

    }

    #[Route('/admin/continent/list', name: 'admin_continent_list', methods: ['GET'])]
    public function listContinent(Request $request, ContinentRepository $continentRepository) {

        $continents = $continentRepository->findAll();

        return $this->render('admin/continent/list.html.twig', [
            'continents' => $continents
        ]);

    }

    #[Route('/admin/continent/show/{slugContinent}', name: 'admin_continent_show', methods: ['GET'])]
    public function showContinent(string $slugContinent,
                                  ContinentRepository $continentRepository,
                                  CountryRepository $countryRepository) {

        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);
        $countries = $countryRepository->findBy(['continent' => $continent]);

        return $this->render('admin/continent/show.html.twig', [
            'continent' => $continent,
            'countries' => $countries
        ]);

    }

    #[Route('/admin/continent/update/{slugContinent}', name: 'continent_update', methods: ['GET', 'POST'])]
    public function updateContinent(string $slugContinent,
                                    Request $request,
                                    EntityManagerInterface $entityManager,
                                    ContinentRepository $continentRepository,
                                    SluggerInterface $slugger)
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {

            $continent->setSlugFromName($slugger);

            $entityManager->persist($continent);
            $entityManager->flush();

            $this->addFlash('success', 'Continent bien modifié!');
            return $this->redirectToRoute('admin_continent_list');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/update.html.twig', [
            'formContinentView' => $formContinentView
        ]);
    }

    #[Route('/admin/continent/delete/{slugContinent}', name: 'continent_delete', methods: ['GET', 'POST'])]
    public function deleteContinent(string $slugContinent,
                                    EntityManagerInterface $entityManager,
                                    ContinentRepository $continentRepository)
    {
        $continent = $continentRepository->findOneBy(['slug' => $slugContinent]);

        $entityManager->remove($continent);
        $entityManager->flush();

        $this->addFlash('success', 'Continent bien supprimé!');
        return $this->redirectToRoute('admin_continent_list');
    }


}