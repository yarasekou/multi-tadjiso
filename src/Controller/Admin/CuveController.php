<?php

namespace App\Controller\Admin;

use App\Entity\Cuve;
use App\Entity\Station;
use App\Form\CuveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/station/{id}/cuves', name: 'admin.station.cuves')]
class CuveController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(Station $station): Response
    {
        return $this->render('admin/gestion-station/cuves/index.html.twig', [
            'station' => $station,
            'cuves' => $station->getCuves()
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, Station $station, EntityManagerInterface $em): Response
    {
        $cuve = new Cuve();
        $cuve->setStation($station);

        $form = $this->createForm(CuveType::class, $cuve, [
            'station' => $station,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cuve->setCreatedAt(new \DateTimeImmutable());
            $em->persist($cuve);
            $em->flush();
            $this->addFlash('success', 'Cuve ajoutée avec succès.');

            return $this->redirectToRoute('admin.station.cuves', ['id' => $station->getId()]);
        }

        return $this->render('admin/gestion-station/cuves/new.html.twig', [
            'station' => $station,
            'form' => $form->createView(),
            'title' => 'Ajouter une cuves'
        ]);
    }

    #[Route('/{cuveId}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, $cuveId, EntityManagerInterface $em): Response
    {
        $cuve = $em->getRepository(Cuve::class)->find($cuveId);
        $form = $this->createForm(CuveType::class, $cuve, [
            'station' => $station,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cuve->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Cuve mise à jour.');

            return $this->redirectToRoute('admin.station.cuves', ['id' => $station->getId()]);
        }

        return $this->render('admin/gestion-station/cuves/edit.html.twig', [
            'station' => $station,
            'form' => $form->createView(),
            'title' => 'Modifier la cuves',
            'cuve' => $cuve,
        ]);
    }

    #[Route('/{cuveId}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, Cuve $cuve, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cuve->getId(), $request->request->get('_token'))) {
            $em->remove($cuve);
            $em->flush();
            $this->addFlash('danger', 'Cuve supprimée.');
        }

        return $this->redirectToRoute('admin.station.cuves.index', ['id' => $station->getId()]);
    }
}
