<?php

namespace App\Controller\Admin;

use App\Repository\SuperAdmin\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StructureController extends AbstractController
{
    #[Route('/admin/structure', name: 'admin.structure')]
    public function show(UserRepository $userRepository): Response
    {
        $adminUser = $userRepository->findAdminByStructure($this->getUser()->getStructure()->getId());

        return $this->render('admin/structure/show.html.twig', [
            'structure' => $this->getUser()->getStructure(),
            'admin_user' => $adminUser,
        ]);
    }
}
