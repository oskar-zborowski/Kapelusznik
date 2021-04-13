<?php

namespace App\Entity;

use App\Repository\AgreementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgreementRepository::class)
 */
class Agreement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="agreements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_required;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_added;

    /**
     * @ORM\Column(type="date")
     */
    private $date_of_entry;

    /**
     * @ORM\OneToMany(targetEntity=UserAgreement::class, mappedBy="agreement", orphanRemoval=true)
     */
    private $userAgreements;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $signature;

    /**
     * @ORM\Column(type="smallint")
     */
    private $version;

    /**
     * @ORM\Column(type="boolean")
     */
    private $in_registration_form;

    public function __construct()
    {
        $this->userAgreements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsRequired(): ?bool
    {
        return $this->is_required;
    }

    public function setIsRequired(bool $is_required): self
    {
        $this->is_required = $is_required;

        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded(\DateTimeInterface $date_added): self
    {
        $this->date_added = $date_added;

        return $this;
    }

    public function getDateOfEntry(): ?\DateTimeInterface
    {
        return $this->date_of_entry;
    }

    public function setDateOfEntry(\DateTimeInterface $date_of_entry): self
    {
        $this->date_of_entry = $date_of_entry;

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
            $userAgreement->setAgreement($this);
        }

        return $this;
    }

    public function removeUserAgreement(UserAgreement $userAgreement): self
    {
        if ($this->userAgreements->removeElement($userAgreement)) {
            // set the owning side to null (unless already changed)
            if ($userAgreement->getAgreement() === $this) {
                $userAgreement->setAgreement(null);
            }
        }

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

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getInRegistrationForm(): ?bool
    {
        return $this->in_registration_form;
    }

    public function setInRegistrationForm(bool $in_registration_form): self
    {
        $this->in_registration_form = $in_registration_form;

        return $this;
    }
}
