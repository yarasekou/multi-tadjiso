<?php

namespace App\Controller\Admin;

use App\Entity\RetourCuve;
use App\Entity\Station;
use App\Form\RetourCuveType;
use App\Repository\RetourCuveRepository;
use App\Repository\TypeCarburantRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('admin/station/{id}/retourcuves', name: 'admin.station.retourcuves')]
class RetourCuveController extends AbstractController
{

    /**
     * @throws Exception
     */
    #[Route('', name: '', methods: ['GET', 'POST'])]
    public function index(Station $station, RetourCuveRepository $retourCuveRepository, PaginatorInterface $paginator, Request $request, TypeCarburantRepository $typeCarburantRepository, EntityManagerInterface $em): Response
    {
        $retourCuves = $paginator->paginate(
            $retourCuveRepository->getStationRetourEnCuves($station->getId()), $request->query->getInt('page', 1), 30);
        $retourCuve = new RetourCuve();
        $form = $this->createForm(RetourCuveType::class, $retourCuve);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($retourCuve);
            $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
            // DateTimeImmutable avec timezone GMT
            $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
            // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
            $createdAt = $createdAt->modify('+1 minute');
            $retourCuve->setCreatedAt($createdAt);
            $retourCuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));
            // CE RETOUR DE CUVE DOIT AJOUTER AUX CUVES C1,C2, ......
            $em->flush();
            $this->addFlash('success', 'Le retour en cuve a été enregistré');
            return $this->redirectToRoute('admin.station.retourcuves',['id' => $station->getId()]);
        }
        return $this->render('admin/gestion-station/retourcuves/index.html.twig', [
            'retourEnCuves' => $retourCuves,
            'form' => $form->createView(),
            'typeCarburants' => $station->getTypeCarburants(),
            'station' => $station,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/retour-en-cuves/{id}/edit", name="gestion_retour_en_cuves.edit", methods={"GET", "POST"})
     * @throws Exception
     */
    #[Route('/{retourcuveId}/edit', name: '.edit')]
    public function edit(Station $station, $retourcuveId, Request $request, TypeCarburantRepository $typeCarburantRepository, EntityManagerInterface $em): Response
    {
        $retourCuve = $em->getRepository(RetourCuve::class)->find($retourcuveId);
        $form = $this->createForm(RetourCuveType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
            // DateTimeImmutable avec timezone GMT
            $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
            // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
            $createdAt = $createdAt->modify('+1 minute');
            $retourCuve->setCreatedAt($createdAt);
            $retourCuve->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));
            $em->flush();
            $this->addFlash('success', 'Le retour en cuve a été modifié');
            return $this->redirectToRoute('admin.station.retourcuves', ['id' => $station->getId()]);
        }
        return $this->render('admin/gestion-station/retourcuves/edit.html.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $station->getTypeCarburants(),
            'retourEnCuve' => $retourCuve,
            'station' => $station
        ]);
    }
    #[Route('/{retourcuveId}/delete', name: '.delete')]
    public function delete(Station $station, $retourcuveId, Request $request, EntityManagerInterface $em): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $retourCuve = $em->getRepository(RetourCuve::class)->find($retourcuveId);
        if ($this->isCsrfTokenValid('delete' . $retourCuve->getId(), $request->request->get('_token'))) {
            $em->remove($retourCuve);
            $em->flush();
            $this->addFlash('success', 'Le retour en cuve a été suprimé');
        }
        return $this->redirectToRoute('admin.station.retourcuves', ['id' => $station->getId()]);
    }
}
