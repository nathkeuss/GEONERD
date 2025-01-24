<?php

namespace App\Controller\Admin;

use App\Entity\Continent;
use App\Form\ContinentType;
use App\Repository\ContinentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminContinentController extends AbstractController
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
            return $this->redirectToRoute('list_continents');

        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/create_continent.html.twig', [
            'formContinentView' => $formContinentView,
        ]);

    }

    #[Route('/admin/continent/list', name: 'list_continents')]
    public function listContinents(Request $request, ContinentRepository $continentRepository)
    {
        $search = $request->query->get('search', '');

        if (!empty($search)) {
            $continents = $continentRepository->findByContinentName($search);
        } else {
            $continents = $continentRepository->findAll();
        }

        return $this->render('admin/continent/list_continents.html.twig', [
            'continents' => $continents,
            'search' => $search,
        ]);
    }

    #[Route('/admin/continent/{id}/update', name: 'update_continent')]
    public function updateContinent(int $id, Request $request, EntityManagerInterface $entityManager, ContinentRepository $continentRepository)
    {
        $continent = $continentRepository->find($id);

        $formContinent = $this->createForm(ContinentType::class, $continent);
        $formContinent->handleRequest($request);

        if ($formContinent->isSubmitted() && $formContinent->isValid()) {
            $entityManager->persist($continent);
            $entityManager->flush();

            return $this->redirectToRoute('list_continents');
            $this->addFlash('success', 'Continent bien modifié!');
            return $this->redirectToRoute('list_continents');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/update_continent.html.twig', [
            'formContinentView' => $formContinentView,
        ]);

    }

    #[Route('/admin/continent/{id}/delete', name: 'delete_continent')]
    public function deleteContinent(int $id, EntityManagerInterface $entityManager, ContinentRepository $continentRepository)
    {
        $continent = $continentRepository->find($id);

        $entityManager->remove($continent);
        $entityManager->flush();

        $this->addFlash('success', 'Continent bien supprimé!');
        return $this->redirectToRoute('list_continents');
    }

}