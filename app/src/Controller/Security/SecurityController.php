<?php

namespace App\Controller\Security;

use App\Form\Security\PasswordResetRequestType;
use App\Process\Security\PasswordResetRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

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

        if ($form->isSubmitted() && $form->isValid()) {
                $bus->dispatch($passwordResetRequest);
        }

        return $this->redirectToRoute('security_password_reset_request_confirmed');
    }

    #[Route(path: '/password/reset/request/confirmed', name: "security_password_reset_request_confirmed")]
    public function requestResetPasswordConfirmed(): Response
    {
        return $this->render('security/password_reset_request_confirmed.html.twig');
    }

    #[Route(path: "/password/reset/{token}", name:"security_password_reset")]
    public function resetPassword(): Response
    {

    }
}
