<?php

namespace App\Service;

use App\Repository\RolePermissionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

final class PermissionChecker
{
    public function __construct(
        private RolePermissionRepository $rolePermissionRepository,
        private Security $security,
        private RequestStack $requestStack,
        private RouterInterface $router,
    ) {
    }

    public function hasPermission(string $permissionName): bool
    {
        $user = $this->security->getUser();
        if (!$user) {
            return false;
        }

        // Check if user has ROLE_ADMIN - admins always have all permissions
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        $userRole = $user->getRole();
        $rolePermission = $this->rolePermissionRepository->findByRole($userRole);

        if (!$rolePermission) {
            return false;
        }

        foreach ($rolePermission->getPermissions() as $permission) {
            if ($permission->getName() === $permissionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has permission and redirect with flash message if not
     * 
     * @param string $permissionName The permission to check
     * @param string $redirectRoute The route to redirect to (defaults to 'app_dashboard')
     * @return RedirectResponse|null Returns redirect response if permission denied, null otherwise
     */
    public function requirePermissionOrRedirect(string $permissionName, string $redirectRoute = 'app_dashboard'): ?RedirectResponse
    {
        if (!$this->hasPermission($permissionName)) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request && $request->getSession()) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    sprintf('You do not have permission to perform this action. Required: %s', 
                        str_replace('_', ' ', ucfirst($permissionName)))
                );
            }
            
            return new RedirectResponse($this->router->generate($redirectRoute));
        }

        return null;
    }

    public function requirePermission(string $permissionName): void
    {
        if (!$this->hasPermission($permissionName)) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException(
                sprintf('Permission "%s" is required for this action', $permissionName)
            );
        }
    }
}
