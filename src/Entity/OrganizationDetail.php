<?php

namespace App\Entity;

use App\Repository\OrganizationDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrganizationDetailRepository::class)]
class OrganizationDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Der Name darf nicht leer sein.")]
    private $title;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Der Ansprechpartner darf nicht leer sein.")]
    private $contactName;

    #[ORM\Column(type: "text", nullable: true)]
    #[Assert\Length(max: 500, maxMessage: "Die Beschreibung soll max. 500 Zeichen lang sein, da sie für Social Media Zwecke geeignet sein soll")]
    private $description;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $link;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $jobsUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $facebookUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $twitterUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $youtubeUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $instagramUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $linkedinUrl;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Url(message: "Die erfasste URL ist ungültig.")]
    private $fediverseUrl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getJobsUrl(): ?string
    {
        return $this->jobsUrl;
    }

    public function setJobsUrl(?string $jobsUrl): self
    {
        $this->jobsUrl = $jobsUrl;

        return $this;
    }

    public function getFacebookUrl(): ?string
    {
        return $this->facebookUrl;
    }

    public function setFacebookUrl(?string $facebookUrl): self
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    public function getTwitterUrl(): ?string
    {
        return $this->twitterUrl;
    }

    public function setTwitterUrl(?string $twitterUrl): self
    {
        $this->twitterUrl = $twitterUrl;

        return $this;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->youtubeUrl;
    }

    public function setYoutubeUrl(?string $youtubeUrl): self
    {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    public function getInstagramUrl(): ?string
    {
        return $this->instagramUrl;
    }

    public function setInstagramUrl(?string $instagramUrl): self
    {
        $this->instagramUrl = $instagramUrl;

        return $this;
    }

    public function editableClone(): self
    {
        return (new self())
            ->setTitle($this->getTitle())
            ->setContactName($this->getContactName())
            ->setDescription($this->getDescription())
            ->setLink($this->getLink())
            ->setJobsUrl($this->getJobsUrl())
            ->setFacebookUrl($this->getFacebookUrl())
            ->setTwitterUrl($this->getTwitterUrl())
            ->setYoutubeUrl($this->getYoutubeUrl())
            ->setInstagramUrl($this->getInstagramUrl())
            ->setLinkedinUrl($this->getLinkedinUrl());
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): self
    {
        $this->linkedinUrl = $linkedinUrl;

        return $this;
    }

    public function getFediverseUrl(): ?string
    {
        return $this->fediverseUrl;
    }

    public function setFediverseUrl(?string $fediverseUrl): self
    {
        $this->fediverseUrl = $fediverseUrl;

        return $this;
    }
}
