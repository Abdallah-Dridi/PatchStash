<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminManageController extends AbstractController
{
    #[Route('/projects', name: 'app_admin_projects', methods: ['GET'])]
    public function projects(): Response
    {
        return $this->render('admin/projects.html.twig');
    }

    #[Route('/modules', name: 'app_admin_modules', methods: ['GET'])]
    public function modules(): Response
    {
        return $this->render('admin/modules.html.twig');
    }

    #[Route('/assets', name: 'app_admin_assets', methods: ['GET'])]
    public function assets(): Response
    {
        return $this->render('admin/assets.html.twig');
    }

    #[Route('/patch-cycles', name: 'app_admin_patch_cycles', methods: ['GET'])]
    public function patchCycles(): Response
    {
        return $this->render('admin/patch_cycles.html.twig');
    }

    #[Route('/vulnerabilities', name: 'app_admin_vulnerabilities', methods: ['GET'])]
    public function vulnerabilities(): Response
    {
        return $this->render('admin/vulnerabilities.html.twig');
    }

    #[Route('/reports', name: 'app_admin_reports', methods: ['GET'])]
    public function reports(): Response
    {
        return $this->render('admin/reports.html.twig');
    }
}
