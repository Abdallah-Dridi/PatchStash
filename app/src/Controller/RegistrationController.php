<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/signup', name: 'app_signup', methods: ['GET', 'POST'])]
    public function signup(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        #[Autowire('%app.mailer_from%')] string $fromAddress
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                (string) $form->get('plainPassword')->getData()
            );

            $user->setPassword($hashedPassword);
            $user->setRole('ROLE_USER');
            $user->setIsVerified(false);

            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->setVerificationCode($code);
            $user->setVerificationExpiresAt(new \DateTimeImmutable('+15 minutes'));

            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from($fromAddress)
                ->to($user->getEmail())
                ->subject('Your Patchstash verification code')
                ->htmlTemplate('email/verification.html.twig')
                ->context([
                    'username' => $user->getUsername(),
                    'code' => $code,
                    'expiresAt' => $user->getVerificationExpiresAt(),
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('app_verify', [
                'userId' => $user->getId(),
            ]);
        }

        return $this->render('security/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: 'app_verify', methods: ['GET', 'POST'])]
    public function verify(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userId = $request->query->getInt('userId');
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found for verification.');
        }

        if ($user->isVerified()) {
            $this->addFlash('success', 'Account already verified. You can sign in.');
            return $this->redirectToRoute('app_login');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $submittedCode = trim((string) $request->request->get('code', ''));
            $expiresAt = $user->getVerificationExpiresAt();
            $now = new \DateTimeImmutable();

            if (
                $submittedCode === $user->getVerificationCode() &&
                $expiresAt !== null &&
                $expiresAt >= $now
            ) {
                $user->setIsVerified(true);
                $user->setVerificationCode(null);
                $user->setVerificationExpiresAt(null);
                $entityManager->flush();

                $this->addFlash('success', 'Your account is verified. You can sign in.');
                return $this->redirectToRoute('app_login');
            }

            $error = 'Invalid or expired code. Please try again.';
        }

        return $this->render('security/verify.html.twig', [
            'user' => $user,
            'error' => $error,
        ]);
    }
}
