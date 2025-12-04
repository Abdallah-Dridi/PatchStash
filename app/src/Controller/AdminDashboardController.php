<?php

namespace App\Controller;

use App\Repository\AssetRepository;
use App\Repository\ModuleRepository;
use App\Repository\PatchCycleRepository;
use App\Repository\ProjectRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use App\Repository\VulnerabilityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    public function __invoke(
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        ModuleRepository $moduleRepository,
        AssetRepository $assetRepository,
        PatchCycleRepository $patchCycleRepository,
        VulnerabilityRepository $vulnerabilityRepository,
        ReportRepository $reportRepository,
    ): Response {
        $stats = [
            ['label' => 'Users', 'value' => $userRepository->count([])],
            ['label' => 'Projects', 'value' => $projectRepository->count([])],
            ['label' => 'Modules', 'value' => $moduleRepository->count([])],
            ['label' => 'Assets', 'value' => $assetRepository->count([])],
            ['label' => 'Patch Cycles', 'value' => $patchCycleRepository->count([])],
            ['label' => 'Vulnerabilities', 'value' => $vulnerabilityRepository->count([])],
            ['label' => 'Reports', 'value' => $reportRepository->count([])],
        ];

        $recentPatchCycles = $patchCycleRepository->findBy([], ['deadline' => 'ASC'], 5);
        $criticalVulnerabilities = $vulnerabilityRepository->findBy([], ['cvss' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'recent_patch_cycles' => $recentPatchCycles,
            'critical_vulnerabilities' => $criticalVulnerabilities,
        ]);
    }
}
