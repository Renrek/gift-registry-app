<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Connection
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'connectionsInitiated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'connectionsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $connectedUser = null;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getConnectedUser(): ?User
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?User $connectedUser): self
    {
        $this->connectedUser = $connectedUser;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }
}
