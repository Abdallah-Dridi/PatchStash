<?php

namespace App\Controller;

use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Repository\ProjectRepository;
use App\Service\PermissionChecker;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ReportController extends AbstractController
{
    #[Route('/admin/reports/new', name: 'app_admin_reports_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository, ReportRepository $reportRepository, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('create_report', 'app_dashboard');
        if ($redirect) return $redirect;
        $report = new Report();

        $form = $this->createFormBuilder($report)
            ->add('title')
            ->add('content')
            ->add('generatedDate')
            ->add('project')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reportRepository->save($report, true);
            return $this->redirectToRoute('app_admin_reports');
        }

        return $this->render('admin/reports/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/reports', name: 'app_report_index', methods: ['GET'])]
    public function index(ReportRepository $reportRepository): Response
    {
        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findAll(),
        ]);
    }
}
