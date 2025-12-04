<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function requestReset(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        #[Autowire('%app.mailer_from%')] string $fromAddress
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        if ($request->isMethod('POST')) {
            $emailAddress = trim((string) $request->request->get('email', ''));
            $user = $emailAddress
                ? $entityManager->getRepository(User::class)->findOneBy(['email' => $emailAddress])
                : null;

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetExpiresAt(new \DateTimeImmutable('+30 minutes'));

                $entityManager->flush();

                $resetLink = $this->generateUrl('app_reset_password', [
                    'uid' => $user->getId(),
                    'token' => $token,
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $email = (new TemplatedEmail())
                    ->from($fromAddress)
                    ->to($user->getEmail())
                    ->subject('Reset your Patchstash password')
                    ->htmlTemplate('email/reset_password.html.twig')
                    ->context([
                        'username' => $user->getUsername(),
                        'resetLink' => $resetLink,
                        'expiresAt' => $user->getResetExpiresAt(),
                    ]);

                $mailer->send($email);
            }

            $this->addFlash('success', 'If an account exists for that email, we sent a reset link.');
            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{uid}/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function reset(
        int $uid,
        string $token,
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = $entityManager->getRepository(User::class)->find($uid);

        if (!$user || !$user->getResetToken() || !hash_equals($user->getResetToken(), $token)) {
            $this->addFlash('error', 'Reset link is invalid. Please request a new one.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $expiresAt = $user->getResetExpiresAt();
        if (!$expiresAt || $expiresAt < new \DateTimeImmutable()) {
            $user->setResetToken(null);
            $user->setResetExpiresAt(null);
            $entityManager->flush();

            $this->addFlash('error', 'Reset link has expired. Please request a new one.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $password = (string) $request->request->get('password', '');
            $confirm = (string) $request->request->get('confirm_password', '');

            if (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setResetToken(null);
                $user->setResetExpiresAt(null);

                $entityManager->flush();

                $this->addFlash('success', 'Password updated. You can sign in.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'user' => $user,
            'error' => $error,
        ]);
    }
}
