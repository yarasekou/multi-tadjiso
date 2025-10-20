<?php

namespace App\Controller\SuperAdmin;

use App\Entity\Structure;
use App\Form\StructureType;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/super-admin/structures', name: 'super-admin.structures')]
class StructureController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(StructureRepository $structureRepository): Response
    {
        return $this->render('super-admin/structures/index.html.twig', [
            'structures' => $structureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $structure = new Structure();
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($structure);
            $entityManager->flush();

            $this->addFlash('success', 'Structure créée avec succès');
            return $this->redirectToRoute('super-admin.structures');
        }

        return $this->render('super-admin/structures/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(Structure $structure): Response
    {
        return $this->render('super-admin/structures/show.html.twig', [
            'structure' => $structure,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Structure $structure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $structure->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Structure modifiée avec succès');
            return $this->redirectToRoute('super-admin.structures');
        }

        return $this->render('super-admin/structures/edit.html.twig', [
            'form' => $form->createView(),
            'structure' => $structure,
        ]);
    }

    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(Request $request, Structure $structure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$structure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($structure);
            $entityManager->flush();

            $this->addFlash('success', 'Structure supprimée avec succès');
        }

        return $this->redirectToRoute('super-admin.structures');
    }
}
