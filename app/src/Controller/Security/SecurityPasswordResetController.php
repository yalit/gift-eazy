<?php

namespace App\Controller\Security;

use App\Form\Security\PasswordResetRequestType;
use App\Form\Security\PasswordResetType;
use App\Process\Security\PasswordReset;
use App\Process\Security\PasswordResetRequest;
use App\Validation\Security\ValidPasswordResetToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityPasswordResetController extends AbstractController
{
    #[Route(path: '/password/reset/request', name: "security_password_reset_request")]
    public function requestResetPassword(Request $request, MessageBusInterface $bus): Response
    {
        $passwordResetRequest = new PasswordResetRequest();
        $form = $this->createForm(PasswordResetRequestType::class, $passwordResetRequest);

        $form->handleRequest($request);

        if (!$form->isSubmitted() or !$form->isValid()) {
            return $this->render('security/password_reset_request.html.twig', [
                'form' => $form,
            ]);
        }

        $bus->dispatch($passwordResetRequest);

        return $this->redirectToRoute('security_password_reset_request_confirmed');
    }

    #[Route(path: '/password/reset/request/confirmed', name: "security_password_reset_request_confirmed")]
    public function requestResetPasswordConfirmed(): Response
    {
        return $this->render('security/password_reset_request_confirmed.html.twig');
    }

    #[Route(path: "/password/reset/{token}", name: "security_password_reset", methods: ["GET", "POST"])]
    public function resetPassword(Request $request, string $token, MessageBusInterface $bus, ValidatorInterface $validator): Response
    {
        if (!$this->isTokenValid($validator, $token)) {
            $this->addFlash('notification', 'pages.security.password_reset.notification.invalid_token');
            return $this->redirectToRoute('app_login');
        }

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($token);

        $form = $this->createForm(PasswordResetType::class, $passwordReset);

        $form->handleRequest($request);

        if (!$form->isSubmitted() or !$form->isValid()) {
            return $this->render('security/password_reset.html.twig', [
                'form' => $form,
            ]);
        }

        $bus->dispatch($passwordReset);
        $this->addFlash('notification', 'pages.security.password_reset.notification.successful_reset');

        return $this->redirectToRoute('app_login');
    }

    private function isTokenValid(ValidatorInterface $validator, string $token): bool
    {
        $violations = $validator->validate($token, new ValidPasswordResetToken());
        return $violations->count() === 0;
    }
}
