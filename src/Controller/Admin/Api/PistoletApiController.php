<?php

namespace App\Controller\Admin\Api;

use App\Entity\TypeCarburant;
use App\Repository\PistoletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PistoletApiController extends AbstractController
{
    /**
     * @Route("/get-pistolets-by-typeCarburant/{id}", methods={"GET"})
     */
    #[Route('/get-pistolets-by-typeCarburant/{id}', methods: ['GET'])]
    public function getPistoletByTypeCarburant(PistoletRepository $pistoletRepository, TypeCarburant $typeCarburant): JsonResponse
    {
        return new JsonResponse($this->serializePistolets(
            $pistoletRepository->findBy(['typeCarburant' => $typeCarburant])
        ));
    }

    private function serializePistolets($pistolets): array
    {
        $pistolets_array = [];
        foreach ($pistolets as $pistolet) {
            $pistolets_array[] = [
                'id' => $pistolet->getId(),
                'numero' => $pistolet->getPompe()->getCode() . ' | ' . $pistolet->getCode()
            ];
        }
        return $pistolets_array;
    }
}
