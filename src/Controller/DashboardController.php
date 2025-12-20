<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard', methods: ['GET'])]
    public function __invoke(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        $userRole = $user->getRole();

        // Check if user has a role assigned
        if (!$userRole || empty(trim($userRole))) {
            return $this->render('dashboard/no_role.html.twig');
        }

        // Route to role-specific dashboard
        return match ($userRole) {
            UserRole::ADMIN->value => $this->renderAdminDashboard($projectRepository),
            UserRole::PROJECT_MANAGER->value => $this->renderProjectManagerDashboard($projectRepository),
            UserRole::OPERATOR->value => $this->renderOperatorDashboard($projectRepository),
            UserRole::AUDITOR->value => $this->renderAuditorDashboard($projectRepository),
            default => $this->render('dashboard/no_role.html.twig'),
        };
    }

    private function renderAdminDashboard(ProjectRepository $projectRepository): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'projects' => $projectRepository->findAll(),
            'totalProjects' => count($projectRepository->findAll()),
        ]);
    }

    private function renderProjectManagerDashboard(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        // Project managers have access to all projects (they manage them)
        return $this->render('dashboard/project_manager.html.twig', [
            'projects' => $projectRepository->findAll(),
            'totalProjects' => count($projectRepository->findAll()),
        ]);
    }

    private function renderOperatorDashboard(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard/operator.html.twig', [
            'projects' => $projectRepository->findAll(),
            'totalProjects' => count($projectRepository->findAll()),
        ]);
    }

    private function renderAuditorDashboard(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard/auditor.html.twig', [
            'projects' => $projectRepository->findAll(),
            'totalProjects' => count($projectRepository->findAll()),
        ]);
    }
}
