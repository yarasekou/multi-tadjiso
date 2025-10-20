<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/super-admin/dashboard', name: 'super-admin.dashboard')]
    // #[IsGranted('ROLE_SUPER_ADMIN')]
    public function dashboardSuperAdmin(): Response
    {
        $user = $this->getUser();

        return $this->render('super-admin/dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin.dashboard')]
    // #[IsGranted('ROLE_ADMIN')]
    public function dashboardAdmin(): Response
    {
        $user = $this->getUser();

        return $this->render('admin/dashboard.html.twig', [
            'user' => $user,
        ]);
    }
}
