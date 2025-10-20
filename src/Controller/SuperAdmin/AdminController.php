<?php

namespace App\Controller\SuperAdmin;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\SuperAdminType;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/super-admin/admins', name: 'super-admin.admins')]
class AdminController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])] // Correction: ajout de '.index'
    public function index(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findAdminsWithAdminRoleAndStructure();

        return $this->render('super-admin/admins/index.html.twig', [
            'users' => $admins,
            'title' => 'Administrateurs avec structure'
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRoleRepository $userRoleRepository
    ): Response
    {
        $user = new User();
        $form = $this->createForm(AdminType::class, $user, [
            'is_creation' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Hasher le mot de passe
                $plainPassword = $form->get('password')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }

                // Définir comme admin et activé
                $user->setLevel(3)
                ->setEnable(true)
                    ->setCreatedAt(new \DateTimeImmutable());

                // Ajouter le rôle ROLE_ADMIN automatiquement
                $adminRole = $userRoleRepository->findOneBy(['name' => 'ADMIN']);
                if ($adminRole) {
                    $user->addUserRole($adminRole);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Administrateur créé avec succès');
                return $this->redirectToRoute('super-admin.admins');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création: ' . $e->getMessage());
            }
        }

        return $this->render('super-admin/admins/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(User $user = null): Response // Ajout de = null pour éviter l'erreur
    {
        if (!$user) {
            $this->addFlash('error', 'Administrateur non trouvé');
            return $this->redirectToRoute('super-admin.admins.index');
        }

        return $this->render('super-admin/admins/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user = null, // Ajout de = null
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if (!$user) {
            $this->addFlash('error', 'Administrateur non trouvé');
            return $this->redirectToRoute('super-admin.admins');
        }

        $form = $this->createForm(AdminType::class, $user, [
            'is_creation' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Gérer le mot de passe si fourni
                $plainPassword = $form->get('password')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }

                $user->setUpdatedAt(new \DateTimeImmutable());
                $entityManager->flush();

                $this->addFlash('success', 'Administrateur modifié avec succès');
                return $this->redirectToRoute('super-admin.admins');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la modification: ' . $e->getMessage());
            }
        }

        return $this->render('super-admin/admins/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user = null, // Ajout de = null
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$user) {
            $this->addFlash('error', 'Administrateur non trouvé');
            return $this->redirectToRoute('super-admin.admins.index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Administrateur supprimé avec succès');
        }

        return $this->redirectToRoute('super-admin.admins');
    }
}
