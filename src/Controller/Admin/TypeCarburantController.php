<?php
// src/Controller/Admin/TypeCarburantController.php

namespace App\Controller\Admin;

use App\Entity\TypeCarburant;
use App\Form\TypeCarburantType;
use App\Repository\TypeCarburantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/carburants')]
#[IsGranted('ROLE_ADMIN')]
class TypeCarburantController extends AbstractController
{
    #[Route('', name: 'admin.carburants', methods: ['GET'])]
    public function index(TypeCarburantRepository $typeCarburantRepository): Response
    {
        $carburants = $typeCarburantRepository->findAllWithStationCount();

        return $this->render('admin/carburants/index.html.twig', [
            'carburants' => $carburants,
        ]);
    }

    #[Route('/new', name: 'admin.carburants.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $typeCarburant = new TypeCarburant();
        $form = $this->createForm(TypeCarburantType::class, $typeCarburant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeCarburant);
            $entityManager->flush();

            $this->addFlash('success', 'Type de carburant créé avec succès.');
            return $this->redirectToRoute('admin.carburants');
        }

        return $this->render('admin/carburants/new.html.twig', [
            'carburant' => $typeCarburant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin.carburants.show', methods: ['GET'])]
    public function show(TypeCarburant $typeCarburant): Response
    {
        return $this->render('admin/carburants/show.html.twig', [
            'carburant' => $typeCarburant,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin.carburants.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        TypeCarburant $typeCarburant,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(TypeCarburantType::class, $typeCarburant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Type de carburant modifié avec succès.');
            return $this->redirectToRoute('admin.carburants');
        }

        return $this->render('admin/carburants/edit.html.twig', [
            'carburant' => $typeCarburant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin.carburants.delete', methods: ['POST'])]
    public function delete(
        Request $request,
        TypeCarburant $typeCarburant,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si le carburant est utilisé par des stations
        if ($typeCarburant->getStations()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce type de carburant car il est utilisé par des stations.');
            return $this->redirectToRoute('admin.carburants');
        }

        if ($this->isCsrfTokenValid('delete'.$typeCarburant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($typeCarburant);
            $entityManager->flush();

            $this->addFlash('success', 'Type de carburant supprimé avec succès.');
        }

        return $this->redirectToRoute('admin.carburants');
    }

    #[Route('/{id}/stations', name: 'admin.carburants.stations', methods: ['GET'])]
    public function stations(TypeCarburant $typeCarburant): Response
    {
        return $this->render('admin/carburants/stations.html.twig', [
            'carburant' => $typeCarburant,
            'stations' => $typeCarburant->getStations(),
        ]);
    }
}
