<?php

namespace App\Service;

use App\Entity\Organization;
use App\Entity\Session;
use App\Entity\Token;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    const FROM_ADDRESS = 'noreply@backend.timetable.ai-week.de';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(Environment $twig, MailerInterface $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function sendUserRegistrationMail(User $user, Token $token): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($user->getEmail())
            ->subject('Deine Registrierung beim AI WEEK Tool')
            ->text(
                $this->twig->render('emails/user_registration.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ])
            );

        $this->mailer->send($message);
    }

    public function sendPasswordResetMail(?User $user, Token $token): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($user->getEmail())
            ->subject('AI WEEK Tool Passwort zurücksetzen')
            ->text(
                $this->twig->render('emails/password_reset.txt.twig', [
                    'user' => $user,
                    'token' => $token->getToken(),
                ])
            );

        $this->mailer->send($message);
    }

    public function sendSessionAwaitingApprovalMail(string $toAddress, Session $session): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Event geändert')
            ->text(
                $this->twig->render('emails/session_awaiting_approval.txt.twig', [
                    'session' => $session,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendOrganizationAwaitingApprovalMail(string $toAddress, Organization $organization): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Veranstalter geändert')
            ->text(
                $this->twig->render('emails/organization_awaiting_approval.txt.twig', [
                    'organization' => $organization,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendSessionCancelledMail(string $toAddress, Session $session): void
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('Event abgesagt')
            ->text(
                $this->twig->render('emails/session_cancelled.txt.twig', [
                    'session' => $session,
                ])
            );

        $this->mailer->send($message);
    }

    public function sendBatchMailNotification(string $toAddress, array $mails)
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($toAddress)
            ->subject('E-Mail-Adressen neuer Organisatoren')
            ->text($this->twig->render('emails/batch_mail_notification.txt.twig', ['addrs' => $mails]));
        $this->mailer->send($message);
    }

    public function sendReporterNotification(Session $session, string $subject, string $templateName)
    {
        $message = (new Email())
            ->from(self::FROM_ADDRESS)
            ->to($session->getOrganization()->getOwner()->getEmail())
            ->subject($subject)
            ->text(
                $this->twig->render(\sprintf('emails/reporter/%s.txt.twig', $templateName), [
                    'session' => $session,
                ])
            );
        $this->mailer->send($message);
    }
}
