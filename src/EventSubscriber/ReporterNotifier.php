<?php

namespace App\EventSubscriber;

use App\Entity\SessionStatus;
use App\Event\SessionStatusChangedEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReporterNotifier implements EventSubscriberInterface
{
    private MailerService $mailerService;

    public static function getSubscribedEvents()
    {
        return [
            SessionStatusChangedEvent::class => 'onSessionStatusChanged',
        ];
    }

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function onSessionStatusChanged(SessionStatusChangedEvent $event)
    {
        if (($event->getOldStatus() === null || $event->getOldStatus() === SessionStatus::Draft)
            && $event->getNewStatus() === SessionStatus::Created) {
            $this->mailerService->sendReporterNotification($event->getSession(), 'Danke für deine Einreichung', 'created');
        } elseif ($event->getOldStatus() === SessionStatus::Created && $event->getNewStatus() === SessionStatus::ModeratorApproved) {
            $this->mailerService->sendReporterNotification($event->getSession(), 'Danke für deinen Event-Vorschlag', 'moderator_approved');
        } elseif ($event->getOldStatus() === SessionStatus::JuryApproved && $event->getNewStatus() === SessionStatus::Scheduled) {
            $this->mailerService->sendReporterNotification($event->getSession(), 'Dein Event wurde angenommen', 'scheduled');
        }
    }

}
