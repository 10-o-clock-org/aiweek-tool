<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_USER_APPROVER = 'ROLE_USER_APPROVER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: "json")]
    private $roles = [];

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: "owner", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $tokens;

    #[ORM\Column(type: "boolean")]
    private bool $registrationComplete;

    #[ORM\OneToMany(targetEntity: Organization::class, mappedBy: "owner", cascade: ["persist", "remove"], orphanRemoval: true)]
    private $organizations;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->tokens = new ArrayCollection();
        $this->organizations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        return null; // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setOwner($this);
        }

        return $this;
    }

    public function createToken(): Token
    {
        $token = Token::generate();
        $this->addToken($token);

        return $token;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getOwner() === $this) {
                $token->setOwner(null);
            }
        }

        return $this;
    }

    public function isRegistrationComplete(): bool
    {
        return $this->registrationComplete;
    }

    public function setRegistrationComplete(bool $registrationComplete): self
    {
        $this->registrationComplete = $registrationComplete;

        return $this;
    }

    public function isEditor(): bool
    {
        return in_array(self::ROLE_EDITOR, $this->getRoles());
    }

    /**
     * @return Collection|Organization[]
     */
    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(Organization $organization): self
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations[] = $organization;
            $organization->setOwner($this);
        }

        return $this;
    }

    public function removeOrganization(Organization $organization): self
    {
        if ($this->organizations->contains($organization)) {
            $this->organizations->removeElement($organization);
            // set the owning side to null (unless already changed)
            if ($organization->getOwner() === $this) {
                $organization->setOwner(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
