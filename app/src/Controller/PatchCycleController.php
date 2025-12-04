<?php

namespace App\Controller;

use App\Entity\PatchCycle;
use App\Form\PatchCycleType;
use App\Repository\PatchCycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patch/cycle')]
final class PatchCycleController extends AbstractController
{
    #[Route(name: 'app_patch_cycle_index', methods: ['GET'])]
    public function index(PatchCycleRepository $patchCycleRepository): Response
    {
        return $this->render('patch_cycle/index.html.twig', [
            'patch_cycles' => $patchCycleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_patch_cycle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $patchCycle = new PatchCycle();
        $form = $this->createForm(PatchCycleType::class, $patchCycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($patchCycle);
            $entityManager->flush();

            return $this->redirectToRoute('app_patch_cycle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('patch_cycle/new.html.twig', [
            'patch_cycle' => $patchCycle,
            'form' => $form,
        ]);
    }

    #[Route('/{cycleId}', name: 'app_patch_cycle_show', methods: ['GET'])]
    public function show(PatchCycle $patchCycle): Response
    {
        return $this->render('patch_cycle/show.html.twig', [
            'patch_cycle' => $patchCycle,
        ]);
    }

    #[Route('/{cycleId}/edit', name: 'app_patch_cycle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PatchCycle $patchCycle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PatchCycleType::class, $patchCycle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_patch_cycle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('patch_cycle/edit.html.twig', [
            'patch_cycle' => $patchCycle,
            'form' => $form,
        ]);
    }

    #[Route('/{cycleId}', name: 'app_patch_cycle_delete', methods: ['POST'])]
    public function delete(Request $request, PatchCycle $patchCycle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$patchCycle->getCycleId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($patchCycle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_patch_cycle_index', [], Response::HTTP_SEE_OTHER);
    }
}
