<?php

namespace App\Controller;

use App\Entity\PatchCycle;
use App\Form\PatchCycleType;
use App\Repository\PatchCycleRepository;
use App\Service\PermissionChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/patch-cycles')]
#[IsGranted('ROLE_ADMIN')]
final class AdminPatchCycleController extends AbstractController
{
    #[Route('', name: 'app_admin_patch_cycles', methods: ['GET'])]
    public function index(PatchCycleRepository $patchCycleRepository): Response
    {
        return $this->render('admin/patch_cycle/index.html.twig', [
            'patch_cycles' => $patchCycleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_patch_cycle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('create_patch_cycle', 'app_admin_patch_cycles');
        if ($redirect) return $redirect;
        
        $patchCycle = new PatchCycle();
        $form = $this->createForm(PatchCycleType::class, $patchCycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($patchCycle);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_patch_cycles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/patch_cycle/new.html.twig', [
            'patch_cycle' => $patchCycle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_patch_cycle_show', methods: ['GET'])]
    public function show(PatchCycle $patchCycle): Response
    {
        return $this->render('admin/patch_cycle/show.html.twig', [
            'patch_cycle' => $patchCycle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_patch_cycle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PatchCycle $patchCycle, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('edit_patch_cycle', 'app_admin_patch_cycles');
        if ($redirect) return $redirect;
        
        $form = $this->createForm(PatchCycleType::class, $patchCycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_patch_cycles', [], Response::HTTP_SEE_OTHER);
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_patch_cycle_delete', ['id' => $patchCycle->getId()]))
            ->setMethod('DELETE')
            ->getForm();

        return $this->render('admin/patch_cycle/edit.html.twig', [
            'patch_cycle' => $patchCycle,
            'form' => $form,
            'delete_form' => $deleteForm,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_patch_cycle_delete', methods: ['POST'])]
    public function delete(Request $request, PatchCycle $patchCycle, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('delete_patch_cycle', 'app_admin_patch_cycles');
        if ($redirect) return $redirect;
        
        if ($this->isCsrfTokenValid('delete'.$patchCycle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($patchCycle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_patch_cycles', [], Response::HTTP_SEE_OTHER);
    }
}
