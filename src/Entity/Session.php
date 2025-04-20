<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\SessionRepository")]
class Session
{
    const DRAFT_NOTIFICATION_DELAY = 'P3D';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $start;

    #[ORM\Column(type: "boolean")]
    private bool $cancelled;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $highlight;

    #[ORM\OneToOne(targetEntity: SessionDetail::class, cascade: ["persist", "remove"], orphanRemoval: false)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SessionDetail $draftDetails;

    #[ORM\OneToOne(targetEntity: SessionDetail::class, cascade: ["persist", "remove"], orphanRemoval: false)]
    private ?SessionDetail $proposedDetails = null;

    #[ORM\OneToOne(targetEntity: SessionDetail::class, cascade: ["persist", "remove"], orphanRemoval: false)]
    private ?SessionDetail $acceptedDetails = null;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: "sessions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $acceptedAt;

    #[ORM\Column(type: 'string', nullable: false, enumType: SessionStatus::class, options: ['default' => SessionStatus::Created])]
    private SessionStatus $status = SessionStatus::Draft;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $draftNotificationDueDate = null;

    public function __construct()
    {
        $this->setCancelled(false);
        $this->setHighlight(false);
        $this->setDraftDetails(new SessionDetail());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }

    public function isHighlight(): bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): Session
    {
        $this->highlight = $highlight;
        return $this;
    }

    public function getDraftDetails(): ?SessionDetail
    {
        return $this->draftDetails;
    }

    public function setDraftDetails(SessionDetail $draftDetails): self
    {
        $this->draftDetails = $draftDetails;

        return $this;
    }

    public function getProposedDetails(): ?SessionDetail
    {
        return $this->proposedDetails;
    }

    public function setProposedDetails(SessionDetail $proposedDetails): self
    {
        $this->proposedDetails = $proposedDetails;

        return $this;
    }

    public function getAcceptedDetails(): ?SessionDetail
    {
        return $this->acceptedDetails;
    }

    public function setAcceptedDetails(?SessionDetail $acceptedDetails): self
    {
        $this->acceptedDetails = $acceptedDetails;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->acceptedDetails === $this->proposedDetails && $this->acceptedDetails !== null;
    }

    public function isRejected(): bool
    {
        return $this->status == SessionStatus::Rejected;
    }

    public function isWaitJury(): bool
    {
        return !$this->cancelled
            && $this->status === SessionStatus::ModeratorApproved
            && $this->acceptedDetails !== null;
    }

    public function isAcceptedAndChanged(): bool
    {
        return $this->acceptedDetails !== null && $this->acceptedDetails !== $this->proposedDetails;
    }

    public function isProposed(): bool
    {
        return $this->proposedDetails !== null;
    }

    public function hasDraft(): bool
    {
        return $this->proposedDetails !== $this->draftDetails;
    }

    public function toSessionWithDetail(bool $withProposedDetails): SessionWithDetail
    {
        $details = $withProposedDetails ? $this->getProposedDetails() : $this->getDraftDetails();

        return (new SessionWithDetail())
            ->setId($this->getId())
            ->setOrganization($this->getOrganization())
            ->setChannel($details->getChannel())
            ->setOnlineOnly($details->getOnlineOnly())
            ->setStart1($details->getStart1())
            ->setStart2($details->getStart2())
            ->setStart3($details->getStart3())
            ->setDuration($details->getDuration())
            ->setTitle($details->getTitle())
            ->setShortDescription($details->getShortDescription())
            ->setLongDescription($details->getLongDescription())
            ->setLocation($details->getLocation())
            ->setLocationLat($details->getLocationLat())
            ->setLocationLng($details->getLocationLng())
            ->setLink($details->getLink());
    }

    public function applyDetails(SessionWithDetail $sessionWithDetail): self
    {
        $this->setOrganization($sessionWithDetail->getOrganization());

        if (!$this->getDraftDetails()->differs($sessionWithDetail)) {
            return $this;
        }

        if (
            $this->getDraftDetails() === $this->getAcceptedDetails() ||
            $this->getDraftDetails() === $this->getProposedDetails()
        ) {
            $this->setDraftDetails(new SessionDetail());
        }

        $this->getDraftDetails()->apply($sessionWithDetail);

        return $this;
    }

    public function propose()
    {
        $this->setProposedDetails($this->getDraftDetails());
        $this->setDraftNotificationDueDate(null);

        if ($this->getStatus() === SessionStatus::Draft) {
            $this->setStatus(SessionStatus::Created);
        }
    }

    public function accept()
    {
        $this->setAcceptedDetails($this->getProposedDetails())->setAcceptedAt(new \DateTimeImmutable('now'));

        if ($this->getStatus() === SessionStatus::Created) {
            $this->setStatus(SessionStatus::ModeratorApproved);
        }
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeInterface
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeInterface $acceptedAt): self
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    public function getStatus(): SessionStatus
    {
        return $this->status;
    }

    public function setStatus(SessionStatus $status): Session
    {
        $this->status = $status;
        return $this;
    }

    public function getDraftNotificationDueDate(): ?\DateTimeInterface
    {
        return $this->draftNotificationDueDate;
    }

    public function setDraftNotificationDueDate(?\DateTimeInterface $draftNotificationDueDate): Session
    {
        $this->draftNotificationDueDate = $draftNotificationDueDate;
        return $this;
    }

    public function scheduleDraftNotification()
    {
        $dueDate = new \DateTime();
        $dueDate->add(new \DateInterval(self::DRAFT_NOTIFICATION_DELAY));

        // If due date falls on weekend (6=Saturday, 0=Sunday), move to next Monday
        $weekday = (int)$dueDate->format('w');
        if ($weekday === 6) {
            $dueDate->modify('+2 days'); // Move from Saturday to Monday
        } elseif ($weekday === 0) {
            $dueDate->modify('+1 day');  // Move from Sunday to Monday
        }

        $this->setDraftNotificationDueDate($dueDate);
    }

}
