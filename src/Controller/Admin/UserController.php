<?php
// src/Controller/Admin/UserController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\SuperAdmin\UserRepository;
use App\Repository\UserRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'admin.users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Récupérer la structure de l'admin connecté
        $currentUser = $this->getUser();
        $structure = $currentUser->getStructure();
        if (!$structure) {
            $this->addFlash('error', 'Aucune structure associée à votre compte.');
            return $this->redirectToRoute('admin.users');
        }

        // Récupérer les utilisateurs de la même structure (niveaux 3 et 4)
        $users = $userRepository->findAllAdminsByStructure($structure->getId());

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'structure' => $structure,
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRoleRepository $userRoleRepository
    ): Response {
        $currentUser = $this->getUser();
        $structure = $currentUser->getStructure();

        if (!$structure) {
            $this->addFlash('error', 'Aucune structure associée à votre compte.');
            return $this->redirectToRoute('admin.dashboard');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_creation' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->render('admin/users/new.html.twig', [
                    'users' => $user,
                    'form' => $form->createView(),
                ]);
            }

            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Assigner la structure et le niveau
            $user->setStructure($structure);

            // Définir les dates
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setEnable(true);
            $user->setLevel(4);
            $adminRole = $userRoleRepository->findOneBy(['name' => 'ADMIN']);
            $user->addUserRole($adminRole);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/users/new.html.twig', [
            'user' => $currentUser,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur appartient à la même structure
        $currentUser = $this->getUser();
        if ($user->getStructure()?->getId() !== $currentUser->getStructure()?->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cet utilisateur.');
            return $this->redirectToRoute('admin.users');
        }

        $form = $this->createForm(UserType::class, $user, ['is_creation' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le mot de passe (seulement si modifié)
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin.users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'currentUser' => $currentUser,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur appartient à la même structure
        $currentUser = $this->getUser();
        if ($user->getStructure()?->getId() !== $currentUser->getStructure()?->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cet utilisateur.');
            return $this->redirectToRoute('admin_users_index');
        }

        // Empêcher la suppression de soi-même
        if ($user->getId() === $currentUser->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('admin_users_index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_users_index');
    }

    #[Route('/{id}/toggle', name: '.toggle', methods: ['POST'])]
    public function toggle(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérifier que l'utilisateur appartient à la même structure
        $currentUser = $this->getUser();
        if ($user->getStructure()?->getId() !== $currentUser->getStructure()?->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cet utilisateur.');
            return $this->redirectToRoute('admin.users');
        }

        // Empêcher la désactivation de soi-même
        if ($user->getId() === $currentUser->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return $this->redirectToRoute('admin.users');
        }

        if ($this->isCsrfTokenValid('toggle'.$user->getId(), $request->request->get('_token'))) {
            $user->setEnable(!$user->isEnable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $status = $user->isEnable() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Utilisateur {$status} avec succès.");
        }

        return $this->redirectToRoute('admin.users');
    }
}
