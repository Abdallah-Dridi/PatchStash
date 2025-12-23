<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Form\AssetType;
use App\Repository\AssetRepository;
use App\Service\PermissionChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/assets')]
#[IsGranted('ROLE_ADMIN')]
final class AdminAssetController extends AbstractController
{
    #[Route('', name: 'app_admin_assets', methods: ['GET'])]
    public function index(AssetRepository $assetRepository): Response
    {
        return $this->render('admin/asset/index.html.twig', [
            'assets' => $assetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_asset_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('create_asset', 'app_admin_assets');
        if ($redirect) return $redirect;
        
        $asset = new Asset();
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($asset);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_assets', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/asset/new.html.twig', [
            'asset' => $asset,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_asset_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Asset $asset): Response
    {
        return $this->render('admin/asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_asset_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Asset $asset, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('edit_asset', 'app_admin_assets');
        if ($redirect) return $redirect;
        
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_assets', [], Response::HTTP_SEE_OTHER);
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_asset_delete', ['id' => $asset->getId()]))
            ->setMethod('DELETE')
            ->getForm();

        return $this->render('admin/asset/edit.html.twig', [
            'asset' => $asset,
            'form' => $form,
            'delete_form' => $deleteForm,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_asset_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Asset $asset, EntityManagerInterface $entityManager, PermissionChecker $permissionChecker): Response
    {
        $redirect = $permissionChecker->requirePermissionOrRedirect('delete_asset', 'app_admin_assets');
        if ($redirect) return $redirect;
        
        if ($this->isCsrfTokenValid('delete'.$asset->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($asset);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_assets', [], Response::HTTP_SEE_OTHER);
    }
}
