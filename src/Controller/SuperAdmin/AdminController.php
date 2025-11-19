<?php

namespace App\Controller\SuperAdmin;

use App\Entity\User;
use App\Form\AdminType;
use App\Repository\SuperAdmin\UserRepository;
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
    #[Route('', name: '', methods: ['GET'])]
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
        UserRoleRepository $userRoleRepository,
        UserRepository $userRepository
    ): Response
    {
        $user = new User();
        $form = $this->createForm(AdminType::class, $user, [
            'is_creation' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Vérifier si la structure a déjà un admin
                $structure = $user->getStructure();
                if ($structure && $userRepository->structureHasAdmin($structure->getId())) {
                    $existingAdmin = $userRepository->findAdminByStructure($structure->getId());
                    $this->addFlash('error', sprintf(
                        'La structure "%s" a déjà un administrateur : %s %s (%s)',
                        $structure->getName(),
                        $existingAdmin->getFirstname(),
                        $existingAdmin->getLastname(),
                        $existingAdmin->getEmail()
                    ));
                    return $this->redirectToRoute('super-admin.admins.new');
                }


                // Hasher le mot de passe
                $plainPassword = $form->get('password')->getData();
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }

                // Définir comme admin et activé
                $user->setEnable(true)
                    ->setLevel(3)
                    ->setCreatedAt(new \DateTimeImmutable());

                // Ajouter le rôle ROLE_ADMIN automatiquement
                $adminRole = $userRoleRepository->findOneBy(['name' => 'ADMIN']);
                if ($adminRole) {
                    $user->addUserRole($adminRole);
                } else {
                    // Créer le rôle ADMIN s'il n'existe pas
                    $adminRole = new \App\Entity\UserRole();
                    $adminRole->setName('ADMIN')
                        ->setDescription('Role des administrateurs');
                    $entityManager->persist($adminRole);
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
    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): Response
    {
        // Sauvegarder la structure actuelle avant le traitement du formulaire
        $currentStructure = $user->getStructure();

        $form = $this->createForm(AdminType::class, $user, [
            'is_creation' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newStructure = $user->getStructure();

                // Vérifier si la structure change et si la nouvelle structure a déjà un admin
                if ($newStructure && $newStructure !== $currentStructure) {
                    if ($userRepository->structureHasAdmin($newStructure->getId())) {
                        $existingAdmin = $userRepository->findAdminByStructure($newStructure->getId());

                        // Si c'est un admin différent, empêcher la modification
                        if ($existingAdmin && $existingAdmin->getId() !== $user->getId()) {
                            $this->addFlash('error', sprintf(
                                'La structure "%s" a déjà un administrateur',
                                $newStructure->getName()
                            ));
                            // Réassigner l'ancienne structure
                            $user->setStructure($currentStructure);
                            return $this->redirectToRoute('super-admin.admins.edit', ['id' => $user->getId()]);
                        }
                    }
                }

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
            'users' => $user,
        ]);
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(User $user): Response // Retirer = null, Symfony gère automatiquement
    {
        return $this->render('super-admin/admins/show.html.twig', [
            'users' => $user,
        ]);
    }

    #[Route('/{id}', name: '.delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'Administrateur supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide');
        }

        return $this->redirectToRoute('super-admin.admins');
    }
}
