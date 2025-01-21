<?php

namespace App\Twig\Components;

use App\Form\Event\EventCreationType;
use App\Process\Event\EventCreation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;

#[AsLiveComponent]
final class EventForm extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    #[LiveProp]
    public ?EventCreation $formData = null;

    /**
     * @return FormInterface<EventCreation>
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EventCreationType::class, $this->formData ?? new EventCreation());
    }

    #[LiveAction]
    public function save(MessageBusInterface $bus): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        // Submit the form! If validation fails, an exception is thrown
        // and the component is automatically re-rendered with the errors
        $this->submitForm();

        /** @var EventCreation $eventCreation */
        $eventCreation = $this->getForm()->getData();

        $bus->dispatch($eventCreation);

        return $this->redirectToRoute('event_creation_anonymous');
    }
}
