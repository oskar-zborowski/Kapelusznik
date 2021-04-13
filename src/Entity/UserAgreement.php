<?php

namespace App\Entity;

use App\Repository\UserAgreementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAgreementRepository::class)
 */
class UserAgreement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userAgreements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Agreement::class, inversedBy="userAgreements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $agreement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_of_accepting;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cancellation_date;

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

    public function getAgreement(): ?Agreement
    {
        return $this->agreement;
    }

    public function setAgreement(?Agreement $agreement): self
    {
        $this->agreement = $agreement;

        return $this;
    }

    public function getDateOfAccepting(): ?\DateTimeInterface
    {
        return $this->date_of_accepting;
    }

    public function setDateOfAccepting(\DateTimeInterface $date_of_accepting): self
    {
        $this->date_of_accepting = $date_of_accepting;

        return $this;
    }

    public function getCancellationDate(): ?\DateTimeInterface
    {
        return $this->cancellation_date;
    }

    public function setCancellationDate(\DateTimeInterface $cancellation_date): self
    {
        $this->cancellation_date = $cancellation_date;

        return $this;
    }
}
