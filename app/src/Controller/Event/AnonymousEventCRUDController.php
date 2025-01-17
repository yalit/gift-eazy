<?php

namespace App\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/event')]
final class AnonymousEventCRUDController extends EventCRUDController
{
    #[Route('/creation', name: 'event_creation_anonymous', methods: ['GET', 'POST'])]
    public function creation(Request $request): Response
    {
        return $this->internalCreation($request);
    }
}
