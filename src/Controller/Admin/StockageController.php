<?php

namespace App\Controller\Admin;

use App\Entity\GlobalStockage;
use App\Entity\Station;
use App\Entity\Stockage;
use App\Form\StockageType;
use App\Repository\CuveRepository;
use App\Repository\GlobalStockageRepository;
use App\Repository\StockageRepository;
use App\Repository\TypeCarburantRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockageController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/admin/station/{id}/stockages', name: 'admin.station.stockages')]
    public function index(Station $station,StockageRepository $stockageRepository, PaginatorInterface $paginator, Request $request, CuveRepository $cuveRepository,
     GlobalStockageRepository $globalStockageRepository, EntityManagerInterface $entityManager): Response
    {
        $stockages = $paginator->paginate($stockageRepository->getStockageByStationId($station->getId(), $request->query->getInt('page', 1), 30));
        $stockage = new Stockage();
        $typeCarburants = $station->getTypeCarburants();
        $stockageGlobals = $paginator->paginate($globalStockageRepository->getGlobalStockagesByStationId($station->getId(), $request->query->getInt('page', 1), 30));

        $form = $this->createForm(StockageType::class, $stockage);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                // Récupération de la cuves
                $cuve = $cuveRepository->find($request->get('cuves'));
                $stockage->setCuve($cuve);
                // Création de la date à partir de la requête
                $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
                // DateTimeImmutable avec timezone GMT
                $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
                // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
                $createdAt = $createdAt->modify('+1 minute');
                // Assignation à l'entité
                $stockage->setCreatedAt($createdAt);
                // Persister en base
                $entityManager->persist($stockage);

                if ($cuve->getCapacity() < ($cuve->getStock() + $stockage->getQuantity() - $stockage->getMissingQuantity())) {
                    $this->addFlash('danger', 'La capacité de ' . $cuve->getNumero() . ' ne permet pas de stocker cette quantité');
                    return $this->render('admin/stockages/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }
                if ($stockage->getQuantity() <= $stockage->getMissingQuantity()) {
                    $this->addFlash('danger', 'La quantité manquante peut pas être superieure à la quantité restante');
                    return $this->render('admin/gestion-station/stockages/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }

                $stockage->setCuve($cuve);
                $result = $globalStockageRepository->getTypeCarburantLastGlobalStockage($stockage->getCuve()->getTypeCarburant());
                if ($result != null) {
                    $stockage->setGloabalStockage($result);
                } else {
                    $this->addFlash('danger', 'Pas de stockage globale pour ce stockage');
                    return $this->render('admin/gestion-station/stockages/index.html.twig', [
                        'stockages' => $stockages,
                        'typeCarburants' => $typeCarburants,
                        'form' => $form->createView(),
                        'stockageGlobals' => $stockageGlobals
                    ]);
                }
                if ($cuve->getStock() === 0) {
                    $cuve->setLastPrixAchatMoyen($cuve->getPrixAchatMoyen());
                    $cuve->setPrixAchatMoyen($stockage->getPurchasePrice());
                } else {
                    $pamp = ($cuve->getStock() * $cuve->getPrixAchatMoyen() + ($stockage->getQuantity() - $stockage->getMissingQuantity()) * $stockage->getPurchasePrice()) / ($cuve->getStock() + $stockage->getQuantity() - $stockage->getMissingQuantity());
                    $cuve->setLastPrixAchatMoyen($cuve->getPrixAchatMoyen());
                    $cuve->setPrixAchatMoyen($pamp);
                }

                $cuve->setStock($cuve->getStock() + $stockage->getQuantity() - $stockage->getMissingQuantity());
                $cuve->setUpdatedAt($createdAt);

                $last = $stockageRepository->findOneBy(['cuves' => $cuve, 'isLast' => true]);
                $last?->setIsLast(false);

                $stockage->setIsLast(true);
                $stockage->setQuantity($stockage->getQuantity() - $stockage->getMissingQuantity());

                $entityManager->flush();

                $this->addFlash('success', 'Le stockage de ' . $cuve->getNumero() . ' a été enregistré !');

                return $this->redirectToRoute('admin.station.stockages', ['id' => $station->getId()]);
            }
        }
        return $this->render('admin/gestion-station/stockages/index.html.twig', [
            'stockages' => $stockages,
            'typeCarburants' => $typeCarburants,
            'form' => $form->createView(),
            'stockageGlobals' => $stockageGlobals,
            'station' => $station
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/add-stockage-global", name="gestion_stockages.addGlobalStockage", methods={"POST"})
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return RedirectResponse
     * @throws Exception
     */

    public function addGlobalStockage(Request $request, TypeCarburantRepository $typeCarburantRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        $globalStockage = new GlobalStockage();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
                // DateTimeImmutable avec timezone GMT
                $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
                // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
                $createdAt = $createdAt->modify('+1 minute');
                // Assignation à l'entité
                $globalStockage->setCreatedAt($createdAt);
            $qte = intval($request->get('quantite'));
            $manquant = intval($request->get('manquant'));
            $prixAchat = intval($request->get('prixAchat'));

            if (!is_int($qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('admin.stockages');
            }

            $globalStockage->setMissingQuantity($manquant)
                ->setPurchasePrice($prixAchat)
                ->setQuantity($qte - $manquant)
                ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $entityManager->persist($globalStockage);
            $entityManager->flush();
            $this->addFlash('success', 'Le stockage global a été enregistré !');
        }

        return $this->redirectToRoute('admin.stockages');
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/{id}/edit-stockage-global", name="gestion_stockages.editGlobalStockage", methods={"GET", "POST"})
     * @param GlobalStockage $globalStockage
     * @param Request $request
     * @param TypeCarburantRepository $typeCarburantRepository
     * @return Response
     * @throws Exception
     */
    public function editGlobalStockage(GlobalStockage $globalStockage, Request $request, TypeCarburantRepository $typeCarburantRepository,  EntityManagerInterface $entityManager): Response
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
                // DateTimeImmutable avec timezone GMT
                $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
                // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
                $createdAt = $createdAt->modify('+1 minute');
                // Assignation à l'entité
                $globalStockage->setCreatedAt($createdAt);
            $qte = intval($request->get('quantite'));
            $manquant = intval($request->get('manquant'));
            $prixAchat = intval($request->get('prixAchat'));

            if (!is_int(value: $qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'modifier ce stockage global');
                return $this->redirectToRoute('admin.stockages');
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('admin.stockages');
            }

            $globalStockage->setMissingQuantity($manquant)
                ->setPurchasePrice($prixAchat)
                ->setQuantity($qte - $manquant)
                ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $entityManager->flush();
            $this->addFlash('success', 'Le stockage gloabal a été modifié');
            return $this->redirectToRoute('admin.stockages');
        }
        return $this->render('admin/stockages/edit.html.twig', [
            'globalStockage' => $globalStockage,
            'typeCarburants' => $station->getTypeCarburants()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/global-stockage/{id}/delete", name="gestion_stockages.deleteGlobalStockage", methods={"DELETE"})
     * @param GlobalStockage $globalStockage
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteGlobalStockage(GlobalStockage $globalStockage, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $globalStockage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $stockages = $globalStockage->getStockages();
            foreach ($stockages as $stockage) {
                $stockage->setGloabalStockage(null);
                $entityManager->flush();
            }
            $entityManager->remove($globalStockage);
            $entityManager->flush();
            $this->addFlash('success', 'Le stockage global a été suprimé');
        }
        return $this->redirectToRoute('gestion_stockages.index');
    }
}
