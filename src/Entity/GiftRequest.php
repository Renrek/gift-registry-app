<?php

namespace App\Entity;

use App\Repository\GiftRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GiftRequestRepository::class)]
class GiftRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdOn = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedOn = null;

    #[ORM\ManyToOne(inversedBy: 'giftRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(type: Types::BINARY)]
    private $fulfilled;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeImmutable
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeImmutable $createdOn): static
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getUpdatedOn(): ?\DateTimeImmutable
    {
        return $this->updatedOn;
    }

    public function setUpdatedOn(\DateTimeImmutable $updatedOn): static
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getFulfilled()
    {
        return $this->fulfilled;
    }

    public function setFulfilled($fulfilled): static
    {
        $this->fulfilled = $fulfilled;

        return $this;
    }
}
