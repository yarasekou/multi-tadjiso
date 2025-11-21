<?php

namespace App\Controller\Admin;

use App\Entity\Indexation;
use App\Entity\Station;
use App\Entity\VentePistolet;
use App\Form\IndexationType;
use App\Repository\IndexationRepository;
use App\Repository\PistoletRepository;
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

#[Route('/admin/station/{id}/indexations', name: 'admin.station.indexations')]
class IndexationController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('', name: '', methods: ['GET', 'POST'])]
    public function index(Station $station,Request $request, IndexationRepository $indexationRepository, PaginatorInterface $paginator, PistoletRepository $pistoletRepository, EntityManagerInterface $em): Response
    {
        $newIndex = new Indexation();
        $form = $this->createForm(IndexationType::class, $newIndex);
        $typeCarburants = $station->getTypeCarburants();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $request->get('btnNewIndex') == '1') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($newIndex);
                $createdAtStr = $request->get('createdAt'); // Exemple : '2025-11-16 12:30'
                // DateTimeImmutable avec timezone GMT
                $createdAt = new DateTimeImmutable($createdAtStr, new DateTimeZone('GMT'));
                // Ajouter 1 minute : comme DateTimeImmutable est immuable, on récupère une nouvelle instance
                $createdAt = $createdAt->modify('+1 minute');

                $newIndex->setCreatedAt($createdAt);
                $pistolet = $pistoletRepository->find($request->get('pistolet'));
                $newIndex->setPistolet($pistolet);
                if ($pistolet->getIndexPistolet() > $newIndex->getValIndex()) {
                    $this->addFlash('danger', 'L\'index de ' . $pistolet->getPompe()->getCode() . ' | ' . $pistolet->getCode() . ' ne pêut pas diminué');
                    return $this->render('admin/gestion-station/indexations/index.html.twig', [
                        'form' => $form->createView(),
                        'typeCarburants' => $typeCarburants,
                        'indexations' => $paginator->paginate($indexationRepository->getStationIndexations(
                            $station->getId()), $request->query->getInt('page', 1), 60),
                        'station' => $station,
                    ]);
                }

                $diff = $newIndex->getValIndex() - $pistolet->getIndexPistolet();
                $newIndex->setDifference($diff);
                $pistolet->setIndexPistolet($newIndex->getValIndex());
                $pistolet->setUpdatedAt($createdAt);
                $ventePistolet = new VentePistolet();
                $ventePistolet->setCreatedAt($createdAt);
                $ventePistolet->setQuantity($diff);
                $ventePistolet->setPistolet($pistolet);
                $ventePistolet->setAmount($diff * $pistolet->getTypeCarburant()->getUnitPrice());
                $em->persist($ventePistolet);
                $em->flush();

                $this->addFlash('success', 'L\'index de ' . $pistolet->getPompe()->getCode() . ' | ' . $pistolet->getCode() . ' a été enregistré');
                return $this->redirectToRoute('admin.station.indexations', ['id' => $station->getId()]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $request->get('btnIndexByDate')) {
            $dateInf = new DateTime($request->get('dateInf'));
            $dateSup = new DateTime($request->get('dateSup'));
            $indexations = $paginator->paginate($indexationRepository->getStationIndexationsByDate(
                $station->getId()
                , $dateInf, $dateSup), $request->query->getInt('page', 1), 1000000000);
            $this->addFlash('success', 'Les index entre ' . $dateInf->format('d-m-Y') . ' et ' . $dateSup->format('d-m-Y'));
            return $this->render('admin/gestion-station/indexations/index.html.twig', [
                'form' => $form->createView(),
                'typeCarburants' => $typeCarburants,
                'indexations' => $indexations,
                'station' => $station,
            ]);
        }


        return $this->render('admin/gestion-station/indexations/index.html.twig', [
            'form' => $form->createView(),
            'typeCarburants' => $typeCarburants,
            'indexations' => $paginator->paginate($indexationRepository->getStationIndexations(
                $station->getId()
            ), $request->query->getInt('page', 1), 60),
            'station' => $station,
        ]);
    }
}
