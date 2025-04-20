<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: "organization", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $sessions;

    #[ORM\OneToOne(targetEntity: OrganizationDetail::class, cascade: ["persist", "remove"], orphanRemoval: false)]
    private ?OrganizationDetail $proposedOrganizationDetails;

    #[ORM\OneToOne(targetEntity: OrganizationDetail::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private ?OrganizationDetail $acceptedOrganizationDetails;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "organizations")]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $logoFileName;

    #[ORM\Column(type: "boolean")]
    private $sendBatchMailNotification = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $goldSponsor = false;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setOrganization($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getOrganization() === $this) {
                $session->setOrganization(null);
            }
        }

        return $this;
    }

    public function getProposedOrganizationDetails(): ?OrganizationDetail
    {
        return $this->proposedOrganizationDetails;
    }

    public function setProposedOrganizationDetails(?OrganizationDetail $proposedOrganizationDetails): self
    {
        $this->proposedOrganizationDetails = $proposedOrganizationDetails;

        return $this;
    }

    public function getAcceptedOrganizationDetails(): ?OrganizationDetail
    {
        return $this->acceptedOrganizationDetails;
    }

    public function setAcceptedOrganizationDetails(?OrganizationDetail $acceptedOrganizationDetails): self
    {
        $this->acceptedOrganizationDetails = $acceptedOrganizationDetails;

        return $this;
    }

    public function ensureEditableOrganizationDetails(): void
    {
        if ($this->proposedOrganizationDetails === null) {
            $this->proposedOrganizationDetails = new OrganizationDetail();
            return;
        }

        if ($this->proposedOrganizationDetails === $this->acceptedOrganizationDetails) {
            $this->proposedOrganizationDetails = $this->acceptedOrganizationDetails->editableClone();
        }
    }

    public function isAccepted(): bool
    {
        return $this->proposedOrganizationDetails !== null &&
            $this->acceptedOrganizationDetails === $this->proposedOrganizationDetails;
    }

    public function isAcceptedAndChanged(): bool
    {
        return $this->acceptedOrganizationDetails !== null &&
            $this->acceptedOrganizationDetails !== $this->proposedOrganizationDetails;
    }

    public function getTitle(): ?string
    {
        return $this->proposedOrganizationDetails ? $this->proposedOrganizationDetails->getTitle() : null;
    }

    public function accept()
    {
        $this->setAcceptedOrganizationDetails($this->proposedOrganizationDetails);
        $this->setSendBatchMailNotification(true);
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLogoFileName(): ?string
    {
        return $this->logoFileName;
    }

    public function setLogoFileName(?string $logoFileName): self
    {
        $this->logoFileName = $logoFileName;

        return $this;
    }

    public function getSendBatchMailNotification(): ?bool
    {
        return $this->sendBatchMailNotification;
    }

    public function setSendBatchMailNotification(bool $sendBatchMailNotification): self
    {
        $this->sendBatchMailNotification = $sendBatchMailNotification;

        return $this;
    }

    public function isGoldSponsor(): bool
    {
        return $this->goldSponsor;
    }

    public function setGoldSponsor(bool $goldSponsor): Organization
    {
        $this->goldSponsor = $goldSponsor;
        return $this;
    }

}
