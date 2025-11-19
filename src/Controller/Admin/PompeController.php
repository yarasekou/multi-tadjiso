<?php

namespace App\Controller\Admin;

use App\Entity\Pompe;
use App\Entity\Station;
use App\Form\PompeType;
use App\Repository\PompeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/stations/{id}/pompes', name: 'admin.station.pompes')]
class PompeController extends AbstractController
{
    #[Route('', name: '')]
    public function index(Station $station, PompeRepository $pompeRepository): Response
    {
        return $this->render('admin/gestion-station/pompes/index.html.twig', [
            'station' => $station,
            'pompes' => $pompeRepository->findBy(['station' => $station])
        ]);
    }

    #[Route('/new', name: '.new')]
    public function new(Request $request, Station $station, EntityManagerInterface $em): Response
    {
        $pompe = new Pompe();

        $form = $this->createForm(PompeType::class, $pompe, [
            'station' => $station,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pompe->setStation($station);
            $pompe->setCreatedAt(new \DateTimeImmutable());

            foreach ($pompe->getPistolets() as $pistolet) {
                $pistolet->setCreatedAt(new \DateTimeImmutable());
            }

            $em->persist($pompe); // persiste aussi tous les pistolets
            $em->flush();

            $this->addFlash('success', 'Pompe créée avec ses pistolets !');
            return $this->redirectToRoute('admin.station.pompes', ['id' => $station->getId()]);
        }

        return $this->render('admin/gestion-station/pompes/new.html.twig', [
            'form' => $form->createView(),
            'station' => $station,
            'title' => 'Ajouter la pompe',
        ]);
    }

    #[Route('/{pompeId}/edit', name: '.edit')]
    public function edit(Request $request, $pompeId, EntityManagerInterface $em): Response
    {
        $pompe = $em->getRepository(Pompe::class)->find($pompeId);
        $form = $this->createForm(PompeType::class, $pompe, [
            'station' => $pompe->getStation(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Date de modification
            $pompe->setUpdatedAt(new \DateTimeImmutable());

            // Nouveaux pistolets : createdAt automatique
            foreach ($pompe->getPistolets() as $pistolet) {
                if ($pistolet->getCreatedAt() === null) {
                    $pistolet->setCreatedAt(new \DateTimeImmutable());
                }
                $pistolet->setUpdatedAt(new \DateTimeImmutable());
            }

            $em->persist($pompe);
            $em->flush();

            $this->addFlash('success', 'Pompe mise à jour avec ses pistolets !');
            return $this->redirectToRoute('admin.station.pompes', ['id' => $pompe->getStation()->getId()]);
        }

        return $this->render('admin/gestion-station/pompes/edit.html.twig', [
            'form' => $form->createView(),
            'station' => $pompe->getStation(),
            'pompe' => $pompe,
            'title' => 'Modifier la pompe',
        ]);
    }

    #[Route('/{pompeId}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Station $station, Pompe $pompe, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pompe->getId(), $request->request->get('_token'))) {
            $em->remove($pompe);
            $em->flush();
            $this->addFlash('success', 'Pompe supprimée !');
        }

        return $this->redirectToRoute('admin.station.pompes.index', [
            'id' => $station->getId()
        ]);
    }
}
