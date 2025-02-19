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
    #[Route('/admin/continent/create', name: 'continent_create', methods: ['GET', 'POST'])]
    public function createContinent(Request $request, EntityManagerInterface $entityManager)
    {
        $continent = new Continent();

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {
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

    #[Route('/admin/continent/update/{id}', name: 'continent_update', methods: ['GET', 'POST'])]
    public function updateContinent(int $id, Request $request, EntityManagerInterface $entityManager, ContinentRepository $continentRepository) {
        $continent = $continentRepository->find($id);

        $formContinent = $this->createForm(ContinentType::class, $continent);

        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {
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

    #[Route('/admin/continent/delete/{id}', name: 'continent_delete', methods: ['POST'])]
    public function deleteContinent(int $id,EntityManagerInterface $entityManager, ContinentRepository $continentRepository) {
        $continent = $continentRepository->find($id);

        $entityManager->remove($continent);
        $entityManager->flush();

        $this->addFlash('success', 'Continent bien supprimé!');
        return $this->redirectToRoute('admin_continent_list');
    }


}