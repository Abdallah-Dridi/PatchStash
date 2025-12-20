<?php

namespace App\Controller;

use App\Enum\UserRole;
use App\Repository\ProjectRepository;
use App\Repository\ModuleRepository;
use App\Repository\AssetRepository;
use App\Repository\PatchCycleRepository;
use App\Repository\VulnerabilityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard', methods: ['GET'])]
    public function __invoke(
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        PatchCycleRepository $patchCycleRepository,
        VulnerabilityRepository $vulnerabilityRepository,
        UserRepository $userRepository
    ): Response
    {
        $user = $this->getUser();
        $userRole = $user->getRole();

        // Check if user has a role assigned
        if (!$userRole || empty(trim($userRole))) {
            return $this->render('dashboard/no_role.html.twig');
        }

        // Route to role-specific dashboard
        return match ($userRole) {
            UserRole::ADMIN->value => $this->renderAdminDashboard($projectRepository, $moduleRepository, $assetRepository, $patchCycleRepository, $vulnerabilityRepository, $userRepository),
            UserRole::PROJECT_MANAGER->value => $this->renderProjectManagerDashboard($projectRepository, $moduleRepository, $assetRepository, $patchCycleRepository, $vulnerabilityRepository),
            UserRole::OPERATOR->value => $this->renderOperatorDashboard($projectRepository, $moduleRepository, $assetRepository, $patchCycleRepository),
            UserRole::AUDITOR->value => $this->renderAuditorDashboard($projectRepository, $moduleRepository, $assetRepository, $vulnerabilityRepository),
            default => $this->render('dashboard/no_role.html.twig'),
        };
    }

    private function renderAdminDashboard(
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        PatchCycleRepository $patchCycleRepository,
        VulnerabilityRepository $vulnerabilityRepository,
        UserRepository $userRepository
    ): Response
    {
        return $this->render('dashboard/admin.html.twig', [
            'users' => $userRepository->findAll(),
            'projects' => $projectRepository->findAll(),
            'modules' => $moduleRepository->findAll(),
            'assets' => $assetRepository->findAll(),
            'patchCycles' => $patchCycleRepository->findAll(),
            'vulnerabilities' => $vulnerabilityRepository->findAll(),
        ]);
    }

    private function renderProjectManagerDashboard(
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        PatchCycleRepository $patchCycleRepository,
        VulnerabilityRepository $vulnerabilityRepository
    ): Response
    {
        return $this->render('dashboard/project_manager.html.twig', [
            'projects' => $projectRepository->findAll(),
            'modules' => $moduleRepository->findAll(),
            'assets' => $assetRepository->findAll(),
            'patchCycles' => $patchCycleRepository->findAll(),
            'vulnerabilities' => $vulnerabilityRepository->findAll(),
        ]);
    }

    private function renderOperatorDashboard(
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        PatchCycleRepository $patchCycleRepository
    ): Response
    {
        return $this->render('dashboard/operator.html.twig', [
            'projects' => $projectRepository->findAll(),
            'modules' => $moduleRepository->findAll(),
            'assets' => $assetRepository->findAll(),
            'patchCycles' => $patchCycleRepository->findAll(),
        ]);
    }

    private function renderAuditorDashboard(
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        VulnerabilityRepository $vulnerabilityRepository
    ): Response
    {
        return $this->render('dashboard/auditor.html.twig', [
            'projects' => $projectRepository->findAll(),
            'modules' => $moduleRepository->findAll(),
            'assets' => $assetRepository->findAll(),
            'vulnerabilities' => $vulnerabilityRepository->findAll(),
        ]);
    }
}
