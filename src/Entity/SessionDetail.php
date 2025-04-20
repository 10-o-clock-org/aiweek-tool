<?php

namespace App\Entity;

use App\DTO\SessionWithDetail;
use App\Repository\SessionDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SessionDetailRepository::class)]
class SessionDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[Assert\NotBlank(message: "Ein Startzeitpunkt muss eingegeben werden.")]
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $start1;

    #[Assert\NotBlank(message: "Ein Alternativtermin muss eingegeben werden.")]
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $start2;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $start3;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $duration;

    #[ORM\Column(type: "string", length: 255)]
    private $title;

    #[ORM\Column(type: "text", nullable: true)]
    private $shortDescription;

    #[ORM\Column(type: "text", nullable: true)]
    private $longDescription;

    #[ORM\Embedded(class: Location::class)]
    private $location;

    #[ORM\Column(type: "float", nullable: true)]
    private $locationLat;

    #[ORM\Column(type: "float", nullable: true)]
    private $locationLng;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $link;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $onlineOnly;

    #[ORM\ManyToOne(targetEntity: Channel::class)]
    private $channel;

    public function __construct()
    {
        $this->location = new Location();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart1(): ?\DateTimeInterface
    {
        return $this->start1;
    }

    public function setStart1(?\DateTimeInterface $start1): SessionDetail
    {
        $this->start1 = $start1;
        return $this;
    }

    public function getStart2(): ?\DateTimeInterface
    {
        return $this->start2;
    }

    public function setStart2(?\DateTimeInterface $start2): SessionDetail
    {
        $this->start2 = $start2;
        return $this;
    }

    public function getStart3(): ?\DateTimeInterface
    {
        return $this->start3;
    }

    public function setStart3(?\DateTimeInterface $start3): SessionDetail
    {
        $this->start3 = $start3;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): SessionDetail
    {
        $this->duration = $duration;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationLat(): ?float
    {
        return $this->locationLat;
    }

    public function setLocationLat(?float $locationLat): self
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    public function getLocationLng(): ?float
    {
        return $this->locationLng;
    }

    public function setLocationLng(?float $locationLng): self
    {
        $this->locationLng = $locationLng;

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

    public function getOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    public function setOnlineOnly(bool $onlineOnly): self
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    public function apply(SessionWithDetail $sessionWithDetail)
    {
        $this
            ->setStart1($this->convertToDateTime($sessionWithDetail->getDate1(), $sessionWithDetail->getStart1()))
            ->setStart2($this->convertToDateTime($sessionWithDetail->getDate2(), $sessionWithDetail->getStart2()))
            ->setStart3($this->convertToDateTime($sessionWithDetail->getDate3(), $sessionWithDetail->getStart3()))
            ->setDuration($sessionWithDetail->getDuration())
            ->setOnlineOnly($sessionWithDetail->getOnlineOnly())
            ->setTitle($sessionWithDetail->getTitle())
            ->setShortDescription($sessionWithDetail->getShortDescription())
            ->setLongDescription($sessionWithDetail->getLongDescription())
            ->setChannel($sessionWithDetail->getChannel())
            ->setLocation($sessionWithDetail->getLocation())
            ->setLocationLat($sessionWithDetail->getLocationLat())
            ->setLocationLng($sessionWithDetail->getLocationLng())
            ->setLink($sessionWithDetail->getLink());
    }

    public function differs(SessionWithDetail $sessionWithDetail): bool
    {
        return $this->getStart1() !== $sessionWithDetail->getStart1() ||
            $this->getStart2() !== $sessionWithDetail->getStart2() ||
            $this->getStart3() !== $sessionWithDetail->getStart3() ||
            $this->getDuration() !== $sessionWithDetail->getDuration() ||
            $this->getOnlineOnly() !== $sessionWithDetail->getOnlineOnly() ||
            $this->getTitle() !== $sessionWithDetail->getTitle() ||
            $this->getShortDescription() !== $sessionWithDetail->getShortDescription() ||
            $this->getLongDescription() !== $sessionWithDetail->getLongDescription() ||
            $this->getChannel() !== $sessionWithDetail->getChannel() ||
            $this->getLocation() !== $sessionWithDetail->getLocation() ||
            $this->getLocationLat() !== $sessionWithDetail->getLocationLat() ||
            $this->getLocationLng() !== $sessionWithDetail->getLocationLng() ||
            $this->getLink() !== $sessionWithDetail->getLink();
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    private function convertToDateTime(?\DateTimeInterface $date, ?\DateTimeInterface $start)
    {
        if ($date === null || $start === null) {
            return null;
        }

        return (new \DateTime())
            ->setDate(
                (int) $date->format('Y'),
                (int) $date->format('m'),
                (int) $date->format('d')
            )
            ->setTime(
                (int) $start->format('H'),
                (int) $start->format('i')
            );
    }
}
