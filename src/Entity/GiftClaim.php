<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\GiftClaimRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: GiftClaimRepository::class)]
class GiftClaim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'claims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GiftRequest $giftRequest = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $claimer = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGiftRequest(): ?GiftRequest
    {
        return $this->giftRequest;
    }

    public function setGiftRequest(?GiftRequest $giftRequest): static
    {
        $this->giftRequest = $giftRequest;

        return $this;
    }

    public function getClaimer(): ?User
    {
        return $this->claimer;
    }

    public function setClaimer(?User $claimer): static
    {
        $this->claimer = $claimer;

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
}