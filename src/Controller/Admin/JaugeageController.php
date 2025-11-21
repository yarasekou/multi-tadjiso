<?php

namespace App\Controller\Admin;

use App\Entity\Jaugeage;
use App\Entity\Station;
use App\Entity\VenteCuve;
use App\Form\JaugeageType;
use App\Repository\CuveMesureRepository;
use App\Repository\CuveRepository;
use App\Repository\JaugeageRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[\Symfony\Component\Routing\Attribute\Route('/admin/stations/{id}/jaugeages', name: 'admin.station.jaugeages')]
class JaugeageController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('', name: '', methods: ['GET', 'POST'])]
    public function index(Station $station, JaugeageRepository $jaugeageRepository, PaginatorInterface $paginator, Request $request, CuveRepository $cuveRepository, CuveMesureRepository $cuveMesureRepository, EntityManagerInterface $em): Response
    {
        $jaugeages = $paginator->paginate($jaugeageRepository->getJaugeageByStation($station->getId()), $request->query->getInt('page', 1), 30);
        $typeCarburants = $station->getTypeCarburants();
        $jaugeage = new Jaugeage();
        $form = $this->createForm(JaugeageType::class, $jaugeage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($jaugeage);
            $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
            // DateTimeImmutable avec timezone GMT
            $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
            // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
            $createdAt = $createdAt->modify('+1 minute');
            $jaugeage->setCreatedAt($createdAt);
            $cuve = $cuveRepository->find($request->get('cuve'));
            $jaugeage->setCuve($cuve);
            $level = intval($jaugeage->getQuantity());
            $mesure = $cuveMesureRepository->findOneBy(['levelCm' => $level, 'cuve' => $cuve]);
            $valVirgule = $jaugeage->getQuantity() - $level;

            if ($valVirgule !== 0) {
                $nextMesure = $cuveMesureRepository->findOneBy(['levelCm' => ($level + 1), 'cuve' => $cuve]);
            }

            if ($mesure === null) {
                $this->addFlash('danger', 'Impossible de trouver cette valeur pour la ' . $cuve->getCode(). ' - '. $cuve->getTypeCarburant()->getName());
                return $this->render('admin/gestion-station/jaugeages/index.html.twig', [
                    'jaugeages' => $jaugeages,
                    'form' => $form->createView(),
                    'typeCarburants' => $typeCarburants,
                    'station' => $station,
                ]);
            }

            if ($valVirgule !== 0 && $nextMesure === null) {
                $this->addFlash('danger', 'Impossible de trouver le niveau ' . ($level + 1) . ' pour la ' . $cuve->getNumero(). ' - '. $cuve->getTypeCarburant()->getName());

                return $this->render('admin/gestion-station/jaugeages/index.html.twig', [
                    'jaugeages' => $jaugeages,
                    'form' => $form->createView(),
                    'typeCarburants' => $typeCarburants,
                    'station' => $station
                ]);
            }

            if ($valVirgule !== 0) {
                $qte = $mesure->getVolume() + ($nextMesure->getVolume() - $mesure->getVolume()) * $valVirgule;
            } else {
                $qte = $mesure->getVolume();
            }

            $diff = $cuve->getStock() - $qte;

            if ($diff < 0) {
                $this->addFlash('danger', 'La quantité jaugée peut pas être superieur au stock de ' . $cuve->getNumero(). ' - '. $cuve->getTypeCarburant()->getName());

                return $this->render('user-client/gestion/jaugeage/index.html.twig', [
                    'jaugeages' => $jaugeages,
                    'form' => $form->createView(),
                    'typeCarburants' => $typeCarburants,

                    'station' => $station,
                ]);
            }

            $venteCuve = new VenteCuve();
            $venteCuve->setCreatedAt($createdAt);
            $venteCuve->setQuantity($diff);
            $venteCuve->setPurchaseAmount($venteCuve->getQuantity() * $cuve->getAveragePurchasePrice());
            $venteCuve->setSaleAmount($venteCuve->getQuantity() * $cuve->getTypeCarburant()->getUnitPrice());
            $venteCuve->setProfit($venteCuve->getSaleAmount() - $venteCuve->getPurchaseAmount());
            $venteCuve->setCuve($cuve);

            $lastJauageage = $jaugeageRepository->findOneBy(['cuve' => $cuve, 'isLast' => true]);
            if ($lastJauageage != null) {
                $lastJauageage->setIsLast(false);
                $lastJauageage->setUpdatedAt($createdAt);
            }

            $jaugeage->setIsLast(true);
            $jaugeage->setQuantity($qte);

            $cuve->setStock($qte);
            $cuve->setUpdatedAt($createdAt);
            $em->persist($venteCuve);
            $em->flush();

            $this->addFlash('success', 'Le jaugeage de ' . $cuve->getCode(). ' - '. $cuve->getTypeCarburant()->getName() . ' a été enregistré');
            return $this->redirectToRoute('admin.station.jaugeages');
        }
        return $this->render('admin/gestion-station/jaugeages/index.html.twig', [
            'jaugeages' => $jaugeages,
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants,
            'station' => $station,
        ]);
    }
}
