<?php

namespace App\Controller\Admin;

use App\Entity\Continent;
use App\Form\ContinentType;
use App\Repository\ContinentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContinentController extends AbstractController
{
    #[Route('/admin/continent/create', name: 'create_continent')]
    public function createContinent(Request $request, EntityManagerInterface $entityManager)
    {
        $continent = new Continent();

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {
            $entityManager->persist($continent);
            $entityManager->flush();

            $this->addFlash('success', 'Continent bien ajouté!');
            return $this->redirectToRoute('admin_list_continents');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/create.html.twig', [
            'formContinentView' => $formContinentView
        ]);

    }

    #[Route('/admin/continent/list', name: 'admin_list_continents')]
    public function listContinent(Request $request, ContinentRepository $continentRepository) {

        $continents = $continentRepository->findAll();

        return $this->render('admin/continent/list.html.twig', [
            'continents' => $continents
        ]);

    }

    #[Route('/admin/continent/update/{id}', name: 'update_continent')]
    public function updateContinent(int $id, Request $request, EntityManagerInterface $entityManager, ContinentRepository $continentRepository) {
        $continent = $continentRepository->find($id);

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {
            $entityManager->persist($continent);
            $entityManager->flush();

            $this->addFlash('success', 'Continent bien modifié!');
            return $this->redirectToRoute('admin_list_continents');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/update.html.twig', [
            'formContinentView' => $formContinentView
        ]);
    }


}