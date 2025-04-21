<?php

namespace App\Command;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Repository\SessionRepository;
use App\Service\MailerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendFinalDraftNotifications extends Command
{
    private SessionRepository $sessionRepository;
    private MailerService $mailerService;

    public function __construct(SessionRepository $sessionRepository, MailerService $mailerService)
    {
        parent::__construct();
        $this->sessionRepository = $sessionRepository;
        $this->mailerService = $mailerService;
    }

    protected function configure()
    {
        $this->setName('app:send-final-draft-notifications')->setDescription(
            'Send final draft notifications'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sessions = $this->sessionRepository->findDraftOnlySessions();

        foreach ($sessions as $session) {
            $session->setDraftNotificationDueDate(null);
            $this->mailerService->sendReporterNotification($session, 'Erinnerung an nicht eingereichtes Event', 'final_draft_notification');
        }

        $this->sessionRepository->flush();

        return 0;
    }
}
