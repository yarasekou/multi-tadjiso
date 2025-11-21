<?php

namespace App\Controller\Admin;

use App\Entity\GlobalStockage;
use App\Entity\Station;
use App\Entity\Stockage;
use App\Form\GlobalStockageType;
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

class GestionStockageController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/admin/station/{id}/stockages', name: 'admin.station.stockages', methods: ['GET', 'POST'])]
    public function index(Station $station,StockageRepository $stockageRepository, PaginatorInterface $paginator, Request $request, CuveRepository $cuveRepository,
                          GlobalStockageRepository $globalStockageRepository, EntityManagerInterface $entityManager): Response
    {
        $stockages = $paginator->paginate($stockageRepository->getStockageByStationId($station->getId(), $request->query->getInt('page', 1), 30));
        $stockage = new Stockage();
        $typeCarburants = $station->getTypeCarburants();
        $stockageGlobals = $paginator->paginate($globalStockageRepository->getGlobalStockagesByStationId($station->getId(), $request->query->getInt('page', 1), 30));

        $form = $this->createForm(StockageType::class, $stockage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de la cuves
            $cuveId = $request->get('cuve'); // correspond à ton <select id="cuve" name="cuve">
            $cuve = $cuveRepository->find($cuveId);

            if (!$cuve) {
                $this->addFlash('danger', 'Cuve invalide sélectionnée.');
                return $this->redirectToRoute('admin.station.stockages', ['id' => $station->getId()]);
            }
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
                $this->addFlash('danger', 'La capacité de ' . $cuve->getCode().' - '. $cuve->getTypeCarburant()->getName() . ' ne permet pas de stocker cette quantité');
                return $this->render('admin/gestion-station/stockages/stockage.html.twig', [
                    'stockages' => $stockages,
                    'typeCarburants' => $typeCarburants,
                    'form' => $form->createView(),
                    'stockageGlobals' => $stockageGlobals,
                    'station' => $station,
                ]);
            }
            if ($stockage->getQuantity() <= $stockage->getMissingQuantity()) {
                $this->addFlash('danger', 'La quantité manquante peut pas être superieure à la quantité restante');
                return $this->render('admin/gestion-station/stockages/stockage.html.twig', [
                    'stockages' => $stockages,
                    'typeCarburants' => $typeCarburants,
                    'form' => $form->createView(),
                    'stockageGlobals' => $stockageGlobals,
                    'station' => $station,
                ]);
            }

            $stockage->setCuve($cuve);
            $result = $globalStockageRepository->getTypeCarburantLastGlobalStockage($stockage->getCuve()->getTypeCarburant());
            if ($result != null) {
                $stockage->setGloabalStockage($result);
            } else {
                $this->addFlash('danger', 'Pas de stockage globale pour ce stockage');
                return $this->render('admin/gestion-station/stockages/stockage.html.twig', [
                    'stockages' => $stockages,
                    'typeCarburants' => $typeCarburants,
                    'form' => $form->createView(),
                    'stockageGlobals' => $stockageGlobals,
                    'station' => $station,
                ]);
            }
            if ($cuve->getStock() === 0) {
                $cuve->setLastAveragePurchasePrice($cuve->getAveragePurchasePrice());
                $cuve->setAveragePurchasePrice($stockage->getPurchasePrice());
            } else {
                $price = ($cuve->getStock() * $cuve->getAveragePurchasePrice() + ($stockage->getQuantity() - $stockage->getMissingQuantity()) * $stockage->getPurchasePrice()) / ($cuve->getStock() + $stockage->getQuantity() - $stockage->getMissingQuantity());
                $cuve->setLastAveragePurchasePrice($cuve->getAveragePurchasePrice());
                $cuve->setAveragePurchasePrice($price);
            }

            $cuve->setStock($cuve->getStock() + $stockage->getQuantity() - $stockage->getMissingQuantity());
            $cuve->setUpdatedAt($createdAt);

            $last = $stockageRepository->findOneBy(['cuve' => $cuve, 'isLast' => true]);

            $last?->setIsLast(false);

            $stockage->setIsLast(true);
            $stockage->setQuantity($stockage->getQuantity() - $stockage->getMissingQuantity());

            $entityManager->flush();

            $this->addFlash('success', 'Le stockage de ' . $cuve->getCode(). ' '. $cuve->getTypeCarburant()->getName() . ' a été enregistré !');

            return $this->redirectToRoute('admin.station.stockages', ['id' => $station->getId()]);
        }

        return $this->render('admin/gestion-station/stockages/stockage.html.twig', [
            'stockages' => $stockages,
            'typeCarburants' => $typeCarburants,
            'form' => $form->createView(),
            'stockageGlobals' => $stockageGlobals,
            'station' => $station
        ]);
    }
    /**
     * @Security("is_granted('ROLE_CLIENT') and user.getIsEnabled()")
     * @Route("/gestion/stockages/{id}/edit", name="gestion_stockages.edit", methods={"GET", "POST"})
     * @param Stockage $stockage
     * @param Request $request
     * @return Response
     */
    public function edit(Stockage $stockage, Request $request): Response
    {
        // TODO VERIFICATION SI LE STOCK DE LA CUVE NA PAS CHANGER
        $form = $this->createForm(StockageAddType::class, $stockage);
        $lastStockage = null;

        $form->handleRequest($request);
        $typeCarburants = $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            dd($lastStockage);
        }
        return $this->render('user-client/gestion/stockage/edit.html.twig', [
            'stockage' => $stockage,
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/admin/station/{id}/globalstockages/new', name: 'admin.station.globalstockages.new', methods: ['GET', 'POST'])]
    public function addGlobalStockage(Station $station, Request $request, TypeCarburantRepository $typeCarburantRepository, EntityManagerInterface $em): Response
    {
        $globalStockage = new GlobalStockage();
        $form = $this->createForm(GlobalStockageType::class, $globalStockage);
        $form->handleRequest($request);
        $typeCarburants = $station->getTypeCarburants();

        if ($form->isSubmitted() && $form->isValid()) {
            $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
            // DateTimeImmutable avec timezone GMT
            $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
            // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
            $createdAt = $createdAt->modify('+1 minute');

            $globalStockage->setCreatedAt($createdAt);
            $qte = intval($globalStockage->getQuantity());
            $manquant = intval($globalStockage->getMissingQuantity());
            $prixAchat = intval($globalStockage->getPurchasePrice());
            if (!is_int($qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('admin.station.globlstockages.new', ['id' => $station->getId()]);
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('admin.station.globalstockages.new', ['id' => $station->getId()]);
            }

            $globalStockage->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $em->persist($globalStockage);
            $em->flush();
            $this->addFlash('success', 'Le stockage global a été enregistré !');
            return $this->redirectToRoute('admin.station.stockages', ['id' => $station->getId()]);
        }

        return $this->render('admin/gestion-station/stockages/globalstockage.html.twig', [
            'typeCarburants' => $typeCarburants,
            'form' => $form->createView(),
            'station' => $station
        ]);
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
    public function editGlobalStockage(GlobalStockage $globalStockage, Request $request, TypeCarburantRepository $typeCarburantRepository): Response
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $createdAt = new DateTime($request->get('createdAt'), new DateTimeZone('GMT'));
            $createdAt->modify('+1 minutes');
            $globalStockage->setCreatedAt($createdAt);
            $qte = intval($request->get('quantite'));
            $manquant = intval($request->get('manquant'));
            $prixAchat = intval($request->get('prixAchat'));

            if (!is_int($qte) || !is_int($manquant) || !is_int($prixAchat)) {
                $this->addFlash('danger', 'Impossible d\'modifier ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            if ($manquant >= $qte) {
                $this->addFlash('danger', 'Impossible d\'enregistrer ce stockage global');
                return $this->redirectToRoute('gestion_stockages.index');
            }

            $globalStockage->setManquant($manquant)
                ->setPrixAchat($prixAchat)
                ->setQuantite($qte - $manquant)
                ->setTypeCarburant($typeCarburantRepository->find($request->get('typeCarburant')));

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le stockage gloabal a été modifié');
            return $this->redirectToRoute('gestion_stockages.index');
        }
        return $this->render('user-client/gestion/stockage/edit_global_stockage.html.twig', [
            'globalStockage' => $globalStockage,
            'typeCarburants' => $this->getUser()->getStructureClient()->getStations()[0]->getTypeCarburants()
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
