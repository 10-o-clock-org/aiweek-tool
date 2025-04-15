<?php

namespace App\DTO;

use App\Entity\Channel;
use App\Entity\Location;
use App\Entity\Organization;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SessionWithDetail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTimeInterface|null
     * @Assert\NotBlank(message="Ein Startzeitpunkt muss eingegeben werden.")
     */
    private $date1;

    /**
     * @var \DateTimeInterface|null
     * @Assert\NotBlank(message="Ein Startzeitpunkt muss eingegeben werden.")
     */
    private $start1;

    /**
     * @var \DateTimeInterface|null
     * @Assert\NotBlank(message="Ein Alternativtermin muss eingegeben werden.")
     */
    private $date2;

    /**
     * @var \DateTimeInterface|null
     * @Assert\NotBlank(message="Ein Alternativtermin muss eingegeben werden.")
     */
    private $start2;

    /**
     * @var \DateTimeInterface|null
     */
    private $date3;

    /**
     * @var \DateTimeInterface|null
     */
    private $start3;

    /**
     * @var int|null
     */
    private $duration;

    /**
     * @var string|null
     * @Assert\NotBlank(message = "Der Titel darf nicht leer sein.")
     * @Assert\Length(max = 30, maxMessage = "Der Titel darf max. 30 Zeichen lang sein.")
     */
    private $title;

    /**
     * @var string|null
     * @Assert\NotBlank(message = "Die Kurzbeschreibung darf nicht leer sein.")
     * @Assert\Length(max = 250, maxMessage = "Die Kurzbeschreibung soll max. 250 Zeichen lang sein, da sie für Social Media Zwecke geeignet sein soll.")
     */
    private $shortDescription;

    /**
     * @var string|null
     * @Assert\NotBlank(message = "Die Langbeschreibung darf nicht leer sein.")
     */
    private $longDescription;

    /**
     * @var Channel|null
     * @Assert\NotNull(message = "Es muss ein Channel ausgewählt sein.")
     */
    private $channel;

    /**
     * @var Organization|null
     * @Assert\NotNull(message = "Es muss ein Veranstalter ausgewählt sein.")
     */
    private $organization;

    /**
     * @var boolean|null
     */
    private $onlineOnly;

    /**
     * @var Location
     * @Assert\Valid()
     */
    private $location;

    /**
     * @var float|null
     */
    private $locationLat;

    /**
     * @var float|null
     */
    private $locationLng;

    /**
     * @var string|null
     * @Assert\AtLeastOneOf(constraints={
     *     @Assert\Email(),
     *     @Assert\Url()
     * },
     *     message="Die erfasste URL ist ungültig (und es handelt sich auch nicht um eine E-Mail-Adresse).",
     *     includeInternalMessages=false)
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SessionWithDetail
    {
        $this->id = $id;
        return $this;
    }

    public function getDate1(): ?\DateTimeInterface
    {
        return $this->date1;
    }

    public function setDate1(?\DateTimeInterface $date1): SessionWithDetail
    {
        $this->date1 = $date1;
        return $this;
    }

    public function getDate2(): ?\DateTimeInterface
    {
        return $this->date2;
    }

    public function setDate2(?\DateTimeInterface $date2): SessionWithDetail
    {
        $this->date2 = $date2;
        return $this;
    }

    public function getDate3(): ?\DateTimeInterface
    {
        return $this->date3;
    }

    public function setDate3(?\DateTimeInterface $date3): SessionWithDetail
    {
        $this->date3 = $date3;
        return $this;
    }

    public function getStart1(): ?\DateTimeInterface
    {
        return $this->start1;
    }

    public function setStart1(?\DateTimeInterface $start1): SessionWithDetail
    {
        $this->start1 = $start1;
        return $this;
    }

    public function getStart2(): ?\DateTimeInterface
    {
        return $this->start2;
    }

    public function setStart2(?\DateTimeInterface $start2): SessionWithDetail
    {
        $this->start2 = $start2;
        return $this;
    }

    public function getStart3(): ?\DateTimeInterface
    {
        return $this->start3;
    }

    public function setStart3(?\DateTimeInterface $start3): SessionWithDetail
    {
        $this->start3 = $start3;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): SessionWithDetail
    {
        $this->duration = $duration;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SessionWithDetail
    {
        $this->title = $title;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): SessionWithDetail
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): SessionWithDetail
    {
        $this->longDescription = $longDescription;
        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): SessionWithDetail
    {
        $this->channel = $channel;
        return $this;
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

    public function getOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    public function setOnlineOnly(?bool $onlineOnly): SessionWithDetail
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): SessionWithDetail
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): SessionWithDetail
    {
        $this->locationLat = $locationLat;
        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(?float $locationLng): SessionWithDetail
    {
        $this->locationLng = $locationLng;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): SessionWithDetail
    {
        $this->link = $link;
        return $this;
    }
}
