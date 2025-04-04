<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\GiftRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: GiftRequestRepository::class)]
#[HasLifecycleCallbacks]
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

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $fulfilled = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 1;

    /**
     * @var Collection<int, GiftClaim> A collection of GiftClaim entities
     */
    #[ORM\OneToMany(mappedBy: 'giftRequest', targetEntity: GiftClaim::class, cascade: ['persist', 'remove'])]
    private Collection $claims;

    public function __construct()
    {
        $this->claims = new ArrayCollection();
    }

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

    #[ORM\PrePersist]
    public function setCreatedOnValue(): void
    {
        $this->createdOn = new \DateTimeImmutable();
        $this->setUpdatedOnValue();
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

    #[ORM\PreUpdate]
    public function setUpdatedOnValue(): void
    {
        $this->updatedOn = new \DateTimeImmutable();
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

    public function getFulfilled(): bool
    {
        return $this->fulfilled;
    }

    public function setFulfilled(bool $fulfilled): static
    {
        $this->fulfilled = $fulfilled;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, GiftClaim>
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(GiftClaim $claim): static
    {
        if (!$this->claims->contains($claim)) {
            $this->claims->add($claim);
            $claim->setGiftRequest($this);
        }

        return $this;
    }

    public function removeClaim(GiftClaim $claim): static
    {
        if ($this->claims->removeElement($claim)) {
            // set the owning side to null (unless already changed)
            if ($claim->getGiftRequest() === $this) {
                $claim->setGiftRequest(null);
            }
        }

        return $this;
    }

    public function getTotalClaimedQuantity(): int
    {
        return array_reduce($this->claims->toArray(), fn($sum, $claim) => $sum + $claim->getQuantity(), 0);
    }

    public function isFullyClaimed(): bool
    {
        return $this->getTotalClaimedQuantity() >= $this->quantity;
    }
}
