<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\RolePermission;
use App\Enum\UserRole;
use App\Repository\PermissionRepository;
use App\Repository\RolePermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/roles')]
#[IsGranted('ROLE_ADMIN')]
final class RolePermissionController extends AbstractController
{
    #[Route('', name: 'app_admin_roles', methods: ['GET'])]
    public function index(RolePermissionRepository $rolePermissionRepository): Response
    {
        $roles = [];
        foreach (UserRole::cases() as $role) {
            $rolePermission = $rolePermissionRepository->findByRole($role->value);
            $roles[] = [
                'name' => $role->value,
                'value' => $role,
                'permissions' => $rolePermission ? $rolePermission->getPermissions() : [],
            ];
        }

        return $this->render('admin/roles/index.html.twig', [
            'roles' => $roles,
        ]);
    }

    #[Route('/{role}/edit', name: 'app_admin_roles_edit', methods: ['GET', 'POST'])]
    public function edit(
        string $role,
        Request $request,
        RolePermissionRepository $rolePermissionRepository,
        PermissionRepository $permissionRepository
    ): Response {
        // Validate role
        $roleExists = false;
        foreach (UserRole::cases() as $userRole) {
            if ($userRole->value === $role) {
                $roleExists = true;
                break;
            }
        }

        if (!$roleExists) {
            throw $this->createNotFoundException('Role not found');
        }

        $rolePermission = $rolePermissionRepository->findByRole($role);
        if (!$rolePermission) {
            $rolePermission = new RolePermission();
            $rolePermission->setRole($role);
        }

        if ($request->isMethod('POST')) {
            $permissionIds = $request->request->all('permissions');
            $rolePermission->getPermissions()->clear();

            foreach ($permissionIds as $id) {
                $permission = $permissionRepository->find($id);
                if ($permission) {
                    $rolePermission->addPermission($permission);
                }
            }

            $rolePermissionRepository->save($rolePermission, true);

            return $this->redirectToRoute('app_admin_roles');
        }

        $allPermissions = $permissionRepository->findAll();
        $selectedPermissionIds = $rolePermission->getPermissions()->map(fn($p) => $p->getPermissionId())->toArray();

        return $this->render('admin/roles/edit.html.twig', [
            'role' => $role,
            'permissions' => $allPermissions,
            'selectedPermissionIds' => $selectedPermissionIds,
        ]);
    }
}
