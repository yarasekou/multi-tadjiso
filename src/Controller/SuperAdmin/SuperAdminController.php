<?php

namespace App\Controller\SuperAdmin;

use App\Entity\User;
use App\Entity\UserRole;
use App\Form\SuperAdminType;
use App\Repository\SuperAdmin\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/super-admin/users', name: 'super-admin.users')]
class SuperAdminController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('super-admin/users/index.html.twig', [
            'users' => $userRepository->findAllSuperAdmins()
        ]);
    }

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(SuperAdminType::class, $user, [
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

                // RECHERCHER le rôle existant au lieu d'en créer un nouveau
                $userRoleRepository = $entityManager->getRepository(UserRole::class);
                $superAdminRole = $userRoleRepository->findOneBy(['name' => 'SUPER_ADMIN']);

                if (!$superAdminRole) {
                    // Créer le rôle seulement s'il n'existe pas
                    $superAdminRole = new UserRole();
                    $superAdminRole->setName('SUPER_ADMIN')
                        ->setDescription('Role des supers administrateurs');
                    $entityManager->persist($superAdminRole);
                }

                $user->addUserRole($superAdminRole);
                $user->setEnable(true)
                    ->setCreatedAt(new \DateTimeImmutable('now'));

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Super administrateur créé avec succès');
                return $this->redirectToRoute('super-admin.users');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création: ' . $e->getMessage());
            }
        }

        return $this->render('super-admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN')] // Minimum: il faut être connecté comme admin
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        Security $security
    ): Response {

        $form = $this->createForm(SuperAdminType::class, $user, [
            'is_creation' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du mot de passe
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword !== null) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('super-admin.users');
        }

        return $this->render('super-admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('super-admin.users');
    }
}
