<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Podany adres e-mail jest już w użyciu")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=6, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $profile_picture;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $external_login_form;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $active_login_form;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_of_birth;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_of_joining;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_blocked;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_logged_in;

    /**
     * @ORM\OneToMany(targetEntity=UserActivity::class, mappedBy="user", orphanRemoval=true)
     */
    private $userActivities;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Agreement::class, mappedBy="creator")
     */
    private $agreements;

    /**
     * @ORM\OneToMany(targetEntity=UserAgreement::class, mappedBy="user", orphanRemoval=true)
     */
    private $userAgreements;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     */
    private $external_authentication;

    public function __construct()
    {
        $this->userActivities = new ArrayCollection();
        $this->agreements = new ArrayCollection();
        $this->userAgreements = new ArrayCollection();
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setProfilePicture(string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getExternalLoginForm(): ?string
    {
        return $this->external_login_form;
    }

    public function setExternalLoginForm(?string $external_login_form): self
    {
        $this->external_login_form = $external_login_form;

        return $this;
    }

    public function getActiveLoginForm(): ?string
    {
        return $this->active_login_form;
    }

    public function setActiveLoginForm(string $active_login_form): self
    {
        $this->active_login_form = $active_login_form;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTimeInterface $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getDateOfJoining(): ?\DateTimeInterface
    {
        return $this->date_of_joining;
    }

    public function setDateOfJoining(\DateTimeInterface $date_of_joining): self
    {
        $this->date_of_joining = $date_of_joining;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->is_blocked;
    }

    public function setIsBlocked(bool $is_blocked): self
    {
        $this->is_blocked = $is_blocked;

        return $this;
    }

    public function getIsLoggedIn(): ?bool
    {
        return $this->is_logged_in;
    }

    public function setIsLoggedIn(bool $is_logged_in): self
    {
        $this->is_logged_in = $is_logged_in;

        return $this;
    }

    /**
     * @return Collection|UserActivity[]
     */
    public function getUserActivities(): Collection
    {
        return $this->userActivities;
    }

    public function addUserActivity(UserActivity $userActivity): self
    {
        if (!$this->userActivities->contains($userActivity)) {
            $this->userActivities[] = $userActivity;
            $userActivity->setUser($this);
        }

        return $this;
    }

    public function removeUserActivity(UserActivity $userActivity): self
    {
        if ($this->userActivities->removeElement($userActivity)) {
            // set the owning side to null (unless already changed)
            if ($userActivity->getUser() === $this) {
                $userActivity->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Agreement[]
     */
    public function getAgreements(): Collection
    {
        return $this->agreements;
    }

    public function addAgreement(Agreement $agreement): self
    {
        if (!$this->agreements->contains($agreement)) {
            $this->agreements[] = $agreement;
            $agreement->setCreator($this);
        }

        return $this;
    }

    public function removeAgreement(Agreement $agreement): self
    {
        if ($this->agreements->removeElement($agreement)) {
            // set the owning side to null (unless already changed)
            if ($agreement->getCreator() === $this) {
                $agreement->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAgreement[]
     */
    public function getUserAgreements(): Collection
    {
        return $this->userAgreements;
    }

    public function addUserAgreement(UserAgreement $userAgreement): self
    {
        if (!$this->userAgreements->contains($userAgreement)) {
            $this->userAgreements[] = $userAgreement;
            $userAgreement->setUser($this);
        }

        return $this;
    }

    public function removeUserAgreement(UserAgreement $userAgreement): self
    {
        if ($this->userAgreements->removeElement($userAgreement)) {
            // set the owning side to null (unless already changed)
            if ($userAgreement->getUser() === $this) {
                $userAgreement->setUser(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getExternalAuthentication(): ?string
    {
        return $this->external_authentication;
    }

    public function setExternalAuthentication(?string $external_authentication): self
    {
        $this->external_authentication = $external_authentication;

        return $this;
    }
}
