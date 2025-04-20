<?php

namespace App\Event;

use App\Entity\Session;
use App\Entity\SessionStatus;
use Symfony\Contracts\EventDispatcher\Event;

class SessionStatusChangedEvent extends Event
{
    private Session $session;
    private ?SessionStatus $oldStatus;
    private SessionStatus $newStatus;

    public function __construct(Session $session, ?SessionStatus $oldStatus, SessionStatus $newStatus)
    {
        if ($session->getId() === null) {
            throw new \InvalidArgumentException('Provided Session instance must be persisted');
        }

        $this->session = $session;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getOldStatus(): ?SessionStatus
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): SessionStatus
    {
        return $this->newStatus;
    }

}
