<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use App\Service\PermissionChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/modules')]
#[IsGranted('ROLE_ADMIN')]
final class AdminModuleController extends AbstractController
{
    #[Route('', name: 'app_admin_modules', methods: ['GET'])]
    public function index(ModuleRepository $moduleRepository): Response
    {
        return $this->render('admin/module/index.html.twig', [
            'modules' => $moduleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_module_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('create_module', 'app_admin_modules');
        if ($redirect) return $redirect;
        
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($module);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_modules', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/module/new.html.twig', [
            'module' => $module,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_module_show', methods: ['GET'])]
    public function show(Module $module): Response
    {
        return $this->render('admin/module/show.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_module_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Module $module, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('edit_module', 'app_admin_modules');
        if ($redirect) return $redirect;
        
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_modules', [], Response::HTTP_SEE_OTHER);
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_module_delete', ['id' => $module->getId()]))
            ->setMethod('DELETE')
            ->getForm();

        return $this->render('admin/module/edit.html.twig', [
            'module' => $module,
            'form' => $form,
            'delete_form' => $deleteForm,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_module_delete', methods: ['POST'])]
    public function delete(Request $request, Module $module, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('delete_module', 'app_admin_modules');
        if ($redirect) return $redirect;
        
        if ($this->isCsrfTokenValid('delete'.$module->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($module);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_modules', [], Response::HTTP_SEE_OTHER);
    }
}
