<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\SessionRepository")]
class Session
{
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
    private ?SessionDetail $proposedDetails;

    #[ORM\OneToOne(targetEntity: SessionDetail::class, cascade: ["persist", "remove"], orphanRemoval: false)]
    private ?SessionDetail $acceptedDetails;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: "sessions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $acceptedAt;

    #[ORM\Column(type: 'string', nullable: false, enumType: SessionStatus::class, options: ['default' => SessionStatus::Created])]
    private SessionStatus $status = SessionStatus::Created;

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
        if (
            $this->getAcceptedDetails() !== null &&
            $this->getAcceptedDetails()->differs($sessionWithDetail)
        ) {
            // If user re-uses an existing, already accepted start=null session, and also updates the details,
            // then re-trigger the editing process.
            $this->setDraftDetails(new SessionDetail());
            $this->setAcceptedDetails(null);
        }

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
    }

    public function accept()
    {
        $this->setAcceptedDetails($this->getProposedDetails())->setAcceptedAt(new \DateTimeImmutable('now'));
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
}
