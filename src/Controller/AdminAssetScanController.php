<?php

namespace App\Controller;

use App\Service\VulnerabilityScannerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminAssetScanController extends AbstractController
{
    #[Route('/admin/assets/scan', name: 'app_admin_asset_scan', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function scan(VulnerabilityScannerService $scannerService): Response
    {
        // Check if user has permission to manage assets (Admin, PM, Operator)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROJECTMANAGER') && !$this->isGranted('ROLE_OPERATOR')) {
            throw $this->createAccessDeniedException();
        }

        try {
            $results = $scannerService->scanAll();
            // ... (rest of the logic remains same)
            $message = sprintf(
                'Scan complete! Scanned %d assets and found %d new vulnerabilities.',
                $results['assets_scanned'],
                $results['new_vulnerabilities']
            );
            
            if ($results['new_vulnerabilities'] > 0) {
                $this->addFlash('success', $message);
            } else {
                $this->addFlash('info', $message);
            }
            
            if (!empty($results['errors'])) {
                foreach ($results['errors'] as $error) {
                    $this->addFlash('error', $error);
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred during scanning: ' . $e->getMessage());
        }

        $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_assets' : 'app_asset_index';
        return $this->redirectToRoute($routeName);
    }

    #[Route('/admin/assets/{id}/scan', name: 'app_admin_asset_scan_single', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function scanSingle(\App\Entity\Asset $asset, VulnerabilityScannerService $scannerService): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROJECTMANAGER') && !$this->isGranted('ROLE_OPERATOR')) {
            throw $this->createAccessDeniedException();
        }

        try {
            $newCount = $scannerService->scanAsset($asset);
            
            if ($newCount > 0) {
                $this->addFlash('success', sprintf('Scan complete! Found %d new vulnerabilities for %s.', $newCount, $asset->getName()));
            } else {
                $this->addFlash('info', sprintf('Scan complete. No new vulnerabilities found for %s.', $asset->getName()));
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred during scanning: ' . $e->getMessage());
        }

        $routeName = $this->isGranted('ROLE_ADMIN') ? 'app_admin_asset_show' : 'app_asset_show';
        return $this->redirectToRoute($routeName, ['id' => $asset->getId()]);
    }
}
