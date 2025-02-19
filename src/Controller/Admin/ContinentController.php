<?php

namespace App\Controller\Admin;

use App\Entity\Continent;
use App\Form\ContinentType;
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
            return $this->redirectToRoute('dashboard_admin');
        }

        $formContinentView = $formContinent->createView();

        return $this->render('admin/continent/create.html.twig', [
            'formContinentView' => $formContinentView
        ]);

    }


}