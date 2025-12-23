<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LandingPageController extends AbstractController
{
    #[Route('/', name: 'app_landing', methods: ['GET'])]
    public function index(): Response
    {
        // If user is logged in, redirect to dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('landing/index.html.twig');
    }
}
