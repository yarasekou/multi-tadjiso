<?php

namespace App\Controller\Admin;

use App\Entity\Station;
use App\Entity\TypeCarburant;
use App\Form\TypeCarburantType;
use App\Repository\TypeCarburantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/typecarburants')]
#[IsGranted('ROLE_ADMIN')]
class TypeCarburantController extends AbstractController
{
    #[Route('/{id}/edit', name: 'admin.typecarburants.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        TypeCarburant $typeCarburant,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(TypeCarburantType::class, $typeCarburant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeCarburant->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->flush();

            $this->addFlash('success', 'Type de carburant modifié avec succès.');
            return $this->redirectToRoute('admin.stations.edit', ['id' => $typeCarburant->getStation()->getId()]);
        }

        return $this->render('admin/gestion-station/carburants/edit.html.twig', [
            'carburant' => $typeCarburant,
            'form' => $form->createView(),
            'station' => $typeCarburant->getStation(),
        ]);
    }

    #[Route('/{id}', name: 'admin.typecarburants.delete', methods: ['POST'])]
    public function delete(
        Request $request,
        TypeCarburant $typeCarburant,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$typeCarburant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($typeCarburant);
            $entityManager->flush();

            $this->addFlash('success', 'Type de carburant supprimé avec succès.');
        }
        return $this->redirectToRoute('admin.stations.edit', ['id' => $typeCarburant->getStation()->getId()]);
    }
}
