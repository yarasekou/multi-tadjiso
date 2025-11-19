<?php
// src/Controller/admin/StationController.php

namespace App\Controller\Admin;

use App\Entity\Station;
use App\Entity\TypeCarburant;
use App\Form\StationType;
use App\Form\TypeCarburantType;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/stations')]
#[IsGranted('ROLE_ADMIN')]
class StationController extends AbstractController
{
    #[Route('', name: 'admin.stations', methods: ['GET'])]
    public function index(StationRepository $stationRepository): Response
    {
        // Récupérer la structure de l'admin connecté
        $currentUser = $this->getUser();
        $structure = $currentUser->getStructure();

        if (!$structure) {
            $this->addFlash('error', 'Aucune structure associée à votre compte.');
            return $this->redirectToRoute('admin.dashboard');
        }

        // Récupérer les stations de la structure
        $stations = $stationRepository->findByStructure($structure->getId());

        return $this->render('admin/stations/index.html.twig', [
            'stations' => $stations,
            'structure' => $structure,
        ]);
    }

    #[Route('/new', name: 'admin.stations.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        StationRepository $stationRepository
    ): Response {
        $currentUser = $this->getUser();
        $structure = $currentUser->getStructure();

        if (!$structure) {
            $this->addFlash('error', 'Aucune structure associée à votre compte.');
            return $this->redirectToRoute('admin.dashboard');
        }

        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assigner la structure
            $station->setStructure($structure);
            $station->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($station);
            $entityManager->flush();

            $this->addFlash('success', 'Station créée avec succès.');
            return $this->redirectToRoute('admin.stations');
        }

        return $this->render('admin/stations/new.html.twig', [
            'station' => $station,
            'form' => $form->createView(),
            'structure' => $structure,
        ]);
    }

    #[Route('/{id}', name: 'admin.stations.show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        // Vérifier que la stations appartient à la même structure
        $currentUser = $this->getUser();
        if ($station->getStructure()?->getId() !== $currentUser->getStructure()?->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette stations.');
            return $this->redirectToRoute('admin.stations');
        }

        return $this->render('admin/stations/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin.stations.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);
        $carburant = new TypeCarburant();
        $formCarburant = $this->createForm(TypeCarburantType::class, $carburant);
        $formCarburant->handleRequest($request);

        // Traitement du formulaire carburant
        if ($formCarburant->isSubmitted() && $formCarburant->isValid()) {
            $carburant->setCreatedAt(new \DateTimeImmutable());
            $carburant->setStation($station);
            $entityManager->persist($carburant);
            $entityManager->flush();
            $this->addFlash('success', 'Carburant ajouté avec succès!');
            return $this->redirectToRoute('admin.stations.edit', ['id' => $station->getId()]);
        }

        // Traitement du formulaire principal
        if ($form->isSubmitted() && $form->isValid()) {
            $station->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'Station modifiée avec succès!');
            return $this->redirectToRoute('admin.stations.show', ['id' => $station->getId()]);
        }

        return $this->render('admin/stations/edit.html.twig', [
            'station' => $station,
            'form' => $form->createView(),
            'carburantForm' => $formCarburant->createView(),
            'carburant' => $carburant,
        ]);
    }

    #[Route('/{id}', name: 'admin.stations.delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Station $station,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que la stations appartient à la même structure
        $currentUser = $this->getUser();
        if ($station->getStructure()?->getId() !== $currentUser->getStructure()?->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette stations.');
            return $this->redirectToRoute('admin.stations');
        }

        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();

            $this->addFlash('success', 'Station supprimée avec succès.');
        }

        return $this->redirectToRoute('admin.stations');
    }
}
