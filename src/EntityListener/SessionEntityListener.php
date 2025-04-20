<?php

namespace App\EntityListener;

use App\Entity\Session;
use App\Entity\SessionStatus;
use App\Event\SessionStatusChangedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Session::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Session::class)]
class SessionEntityListener
{
    const STATUS_FIELD_NAME = 'status';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(Session $session, PostPersistEventArgs $event): void
    {
        $this->eventDispatcher->dispatch(new SessionStatusChangedEvent($session, null,
            $session->getStatus()));
    }

    public function preUpdate(Session $session, PreUpdateEventArgs $event): void
    {
        if (!$event->hasChangedField(self::STATUS_FIELD_NAME)) {
            return;
        }

        $this->eventDispatcher->dispatch(new SessionStatusChangedEvent($session,
            SessionStatus::from($event->getOldValue(self::STATUS_FIELD_NAME)),
            SessionStatus::from($event->getNewValue(self::STATUS_FIELD_NAME))));
    }
}