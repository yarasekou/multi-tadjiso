<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users', name: 'admin.users')]
class UserController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('admin/users/index.html.twig', [
            'controller_name' => 'Admin/UserController',
        ]);
    }
}
