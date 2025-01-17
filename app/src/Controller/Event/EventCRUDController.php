<?php

namespace App\Controller\Event;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class EventCRUDController extends AbstractController
{
    public function internalCreation(Request $request): Response
    {
        return $this->render('event/creation.html.twig');
    }
}
