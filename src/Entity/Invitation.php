<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Invitation
{
    /**
     * @var int|null The identifier of an invitation, managed by the ORM
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $inviter = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $used = false;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private ?string $invitationCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInviter(): ?User
    {
        return $this->inviter;
    }

    public function setInviter(?User $inviter): self
    {
        $this->inviter = $inviter;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): self
    {
        $this->used = $used;
        return $this;
    }

    public function getInvitationCode(): ?string
    {
        return $this->invitationCode;
    }

    public function setInvitationCode(string $invitationCode): self
    {
        $this->invitationCode = $invitationCode;
        return $this;
    }
}