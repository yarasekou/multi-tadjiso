<?php

namespace App\Controller\Admin\Ventes;

use App\Entity\Station;
use App\Repository\BonClientRepository;
use App\Repository\DepenseRepository;
use App\Repository\RetourCuveRepository;
use App\Repository\VentePistoletRepository;
use DateTime;
use DateTimeZone;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/station/{id}/ventepistolets', name: 'admin.station.ventepistolets')]
class VentePistoletController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('', name: '', methods: ['GET'])]
    public function index(Station $station, Request $request, BonClientRepository $bonClientRepository, DepenseRepository $depenseJournalierRepository, VentePistoletRepository $ventePistoletCarburantRepository, RetourCuveRepository $retourEnCuveRepository): Response
    {
        $typeCarburants = $station->getTypeCarburants();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dateInf = $request->get('date_inf');
            $dateSup = $request->get('date_sup');
            $dateD = DateTime::createFromFormat('Y-m-d', $dateInf);
            $dateF = DateTime::createFromFormat('Y-m-d', $dateSup);
        } else {
            $date = date('Y-m-d');
            $dateD = DateTime::createFromFormat('Y-m-d', $date);
            $dateF = DateTime::createFromFormat('Y-m-d', $date);
            $dateD->modify('- 1 days');
        }
        $dateInf = $dateD->format('Y-m-d');
        $dateSup = $dateF->format('Y-m-d');
        $this->addFlash('success', 'Les ventes de ' . $dateInf . ' - ' . $dateSup);
        // DEBUT AVEC ARRAY
        $etatJournalier = array();

        foreach ($typeCarburants as $typeCarburant) {
            $venteByTypeCarburant = $ventePistoletCarburantRepository->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant);
            $qte = 0;
            $montant = 0;
            foreach ($venteByTypeCarburant as $vente) {
                $qte = $qte + $vente->getQuantite();
                $montant = $montant + $vente->getMontant();
            }
            $bons = $bonClientRepository->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());
            $qteBon = 0;
            $montantBon = 0;
            foreach ($bons as $bon) {
                $qteBon = $qteBon + $bon->getQuantite();
                $montantBon = $montantBon + $bon->getMontant();
            }
            $retourEnCuves = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());
            $qteRetour = 0;
            if ($retourEnCuves != []) {
                $qteRetour = 0;
                foreach ($retourEnCuves as $retourEnCuve) {
                    $qteRetour = $qteRetour + $retourEnCuve->getQuantite();
                }
            }

            $etatJournalier[$typeCarburant->getName()] = array(
                'quantite' => $qte,
                'montant' => $qte * $montant,
                'qteBon' => $qteBon,
                'montantBon' => $montantBon,
                'montantNet' => $montant - $montantBon,
                'retourEnCuve' => $qteRetour
            );
        }
        // FIN ARRAY


        // DEBUT
        $arrayVentePistoletTypeCarburant = [];
        $arrayRetourEnCuve = [];

        foreach ($typeCarburants as $typeCarburant) {
            $ventePistoletTypeCarburants = $ventePistoletCarburantRepository->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant->getId());
            $venteGlobalTypeCarburant = new VenteGlobalTypeCarburant();
            $venteGlobalTypeCarburant->setTypeCarburant($typeCarburant);
            $qte = 0;
            $montant = 0;
            foreach ($ventePistoletTypeCarburants as $ventePistoletTypeCarburant) {
                $qte = $qte + $ventePistoletTypeCarburant->getQuantite();
                $montant = $montant + $ventePistoletTypeCarburant->getMontant();
            }
            $venteGlobalTypeCarburant->setQte($qte);
            $venteGlobalTypeCarburant->setMontant($montant);
            $bons = $bonClientRepository->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());
            $qteBon = 0;
            $montantBon = 0;
            foreach ($bons as $bon) {
                $qteBon = $qteBon + $bon->getQuantite();
                $montantBon = $montantBon + $bon->getMontant();
            }
            $venteGlobalTypeCarburant->setQteBon($qteBon);
            $venteGlobalTypeCarburant->setMontantBon($montantBon);
            $venteGlobalTypeCarburant->setMontantNet($montant - $montantBon);

            $arrayVentePistoletTypeCarburant[] = $venteGlobalTypeCarburant;

            $retourEnCuves = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());

            if ($retourEnCuves != []) {
                foreach ($retourEnCuves as $retourEnCuve) {
                    $arrayRetourEnCuve[] = $retourEnCuve;
                }
            }
        }

        $montantGlobal = 0;
        foreach ($arrayVentePistoletTypeCarburant as $venteGlobalTypeCarburant) {
            $montantGlobal = $montantGlobal + $venteGlobalTypeCarburant->getMontantNet();
        }

        $depenses = $depenseJournalierRepository->depensesJournalierByDate($dateInf, $dateSup,
            $station->getId()
        );

        $montantDepenses = 0;

        foreach ($depenses as $depense) {
            $montantDepenses = $montantDepenses + $depense->getMontant();
        }

        $qteRetour = 0;
        $montantRetourEnCuve = 0;

        foreach ($arrayRetourEnCuve as $retourEnCuve) {
            $qteRetour = $qteRetour + $retourEnCuve->getQuantite();
            $montantRetourEnCuve = $montantRetourEnCuve + $retourEnCuve->getQuantite() * $retourEnCuve->getTypeCarburant()->getPrixLittre();
        }

        $montantNet = $montantGlobal - $montantDepenses - $montantRetourEnCuve;

        return $this->render('admin/gestion-station/etats/index.html.twig', [
            'depenses' => $depenses,
            'retourCuves' => $arrayRetourEnCuve,
            'montantNet' => $montantNet,
            'dateInf' => $dateInf,
            'dateSup' => $dateSup,
            'montantDepense' => $montantDepenses,
            'arrayVentePistoletTypeCarburant' => $arrayVentePistoletTypeCarburant,
            'station' => $station,
        ]);

        // FIN
    }

    /**
     * @throws \Exception
     */
    #[Route('/generate-pdf/{dateInf}/and/{dateSup}/ventepistolets', name: '.pdf')]
    public function generatePDF(
        Station $station,
        VentePistoletRepository $ventePistoletCarburantRepository,
        BonClientRepository $bonClientRepository,
        DepenseRepository $depenseJournalierRepository,
        RetourCuveRepository $retourEnCuveRepository,
        string $dateInf,
        string $dateSup
    ): Response {

        $dateDebut = new DateTime($dateInf);
        $dateFin   = new DateTime($dateSup);

        // Sécurité des dates
        if ($dateDebut > $dateFin) {
            $this->addFlash('danger', 'La date de début doit être avant la date de fin.');
            return $this->redirectToRoute('admin.station.ventepistolets', ['id' => $station->getId()]);
        }

        $typeCarburants = $station->getTypeCarburants();

        $arrayVentePistoletTypeCarburant = [];
        $arrayRetourEnCuve = [];

        foreach ($typeCarburants as $typeCarburant) {

            // --- VENTES PAR TYPE ---
            $ventes = $ventePistoletCarburantRepository
                ->ventePistoletCarburantByDate($dateInf, $dateSup, $typeCarburant->getId());

            $qteVente = array_sum(array_map(fn($v) => $v->getQuantite(), $ventes));
            $montantVente = array_sum(array_map(fn($v) => $v->getMontant(), $ventes));

            // --- BONS ---
            $bons = $bonClientRepository
                ->getNotPaidBonClientsByDateByTypeCarburant($dateInf, $dateSup, $typeCarburant->getId());

            $qteBon = array_sum(array_map(fn($b) => $b->getQuantite(), $bons));
            $montantBon = array_sum(array_map(fn($b) => $b->getMontant(), $bons));

            // --- OBJET VENTE GLOBAL ---
            $venteGlobal = new VenteGlobalTypeCarburant();
            $venteGlobal->setTypeCarburant($typeCarburant);
            $venteGlobal->setQte($qteVente);
            $venteGlobal->setMontant($montantVente);
            $venteGlobal->setQteBon($qteBon);
            $venteGlobal->setMontantBon($montantBon);
            $venteGlobal->setMontantNet($montantVente - $montantBon);

            $arrayVentePistoletTypeCarburant[] = $venteGlobal;

            // --- RETOUR EN CUVE ---
            $retours = $retourEnCuveRepository->getRetourEnCuveByDate($dateInf, $dateSup, $typeCarburant->getId());

            foreach ($retours as $retour) {
                $arrayRetourEnCuve[] = $retour;
            }
        }

        // --- TOTAL GLOBAL NET ---
        $montantGlobal = array_sum(array_map(fn($v) => $v->getMontantNet(), $arrayVentePistoletTypeCarburant));

        // --- DÉPENSES ---
        $depenses = $depenseJournalierRepository->depensesJournalierByDate(
            $dateInf,
            $dateSup,
            $this->getUser()->getId()
        );
        $montantDepenses = array_sum(array_map(fn($d) => $d->getMontant(), $depenses));

        // --- RETOUR EN CUVE ---
        $qteRetour = array_sum(array_map(fn($r) => $r->getQuantite(), $arrayRetourEnCuve));
        $montantRetour = array_sum(array_map(fn($r) =>
            $r->getQuantite() * $r->getTypeCarburant()->getPrixLittre(),
            $arrayRetourEnCuve
        ));

        $montantNet = $montantGlobal - $montantDepenses - $montantRetour;

        // --- HTML TWIG ---
        $html = $this->renderView('admin/gestion-station/mypdf.html.twig', [
            'depenses' => $depenses,
            'montantNet' => $montantNet,
            'dateInf' => $dateInf,
            'dateSup' => $dateSup,
            'montantDepense' => $montantDepenses,
            'arrayVentePistoletTypeCarburant' => $arrayVentePistoletTypeCarburant,
            'createdAt' => new DateTime('now')
        ]);

        // --- CONFIG DOMPDF ---
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->setTempDir(sys_get_temp_dir());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return new Response(
            $dompdf->stream("vente-$dateInf-$dateSup.pdf", ["Attachment" => true]),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}
