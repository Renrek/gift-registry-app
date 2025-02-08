<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $invitedBy = null;

    /**
     * @var Collection<int, GiftRequest>
     */
    #[ORM\OneToMany(targetEntity: GiftRequest::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $giftRequests;

    /**
     * @var Collection<int, Connection>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Connection::class, cascade: ['persist', 'remove'])]
    private Collection $connectionsInitiated;

    /**
     * @var Collection<int, Connection>
     */
    #[ORM\OneToMany(mappedBy: 'connectedUser', targetEntity: Connection::class, cascade: ['persist', 'remove'])]
    private Collection $connectionsReceived;

    public function __construct()
    {
        $this->giftRequests = new ArrayCollection();
        $this->connectionsInitiated = new ArrayCollection();
        $this->connectionsReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        if ($this->id === null) {
            throw new \LogicException('ID should not be accessed before persistence.');
        }
        return $this->id;
    } 

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles = array_map('strval', $roles);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        if ($this->password === null) {
            throw new \LogicException('Password should not be accessed before persistence.');
        }
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, GiftRequest>
     */
    public function getGiftRequests(): Collection
    {
        return $this->giftRequests;
    }

    public function addGiftRequest(GiftRequest $giftRequest): static
    {
        if (!$this->giftRequests->contains($giftRequest)) {
            $this->giftRequests->add($giftRequest);
            $giftRequest->setOwner($this);
        }

        return $this;
    }

    public function removeGiftRequest(GiftRequest $giftRequest): static
    {
        if ($this->giftRequests->removeElement($giftRequest)) {
            // set the owning side to null (unless already changed)
            if ($giftRequest->getOwner() === $this) {
                $giftRequest->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnectionsInitiated(): Collection
    {
        return $this->connectionsInitiated;
    }

    /**
     * @return Collection<int, Connection>
     */
    public function getConnectionsReceived(): Collection
    {
        return $this->connectionsReceived;
    }

    public function getInvitedBy(): ?User
    {
        return $this->invitedBy;
    }

    public function setInvitedBy(?User $invitedBy): self
    {
        $this->invitedBy = $invitedBy;
        return $this;
    }
}
