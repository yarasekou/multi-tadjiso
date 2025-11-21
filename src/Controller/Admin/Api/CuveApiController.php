<?php
namespace App\Controller\Admin\Api;

use App\Entity\TypeCarburant;
use App\Repository\CuveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CuveApiController extends AbstractController
{
    #[Route('/get-cuves-by-type-carburant/{id}', methods: ['GET'])]
    public function getCuveByTypeCarburant(TypeCarburant $typeCarburant, CuveRepository $cuveRepository): JsonResponse
    {
        $cuves = $cuveRepository->findBy(['typeCarburant' => $typeCarburant]);
        $result = [];

        foreach ($cuves as $cuve) {
            $result[] = [
                'id' => $cuve->getId(),
                'numero' => $cuve->getCode().' '.$cuve->getTypeCarburant()->getName(),
            ];
        }

        return new JsonResponse($result);
    }
}
