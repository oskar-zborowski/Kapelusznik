<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rooms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $host;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $logo_filename;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=RoomConnection::class, mappedBy="room", orphanRemoval=true)
     */
    private $roomConnections;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $current_question_number;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number_of_questions;

    /**
     * @ORM\OneToMany(targetEntity=RoomQuestion::class, mappedBy="room", orphanRemoval=true)
     */
    private $roomQuestions;

    public function __construct()
    {
        $this->roomConnections = new ArrayCollection();
        $this->roomQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): self
    {
        $this->host = $host;

        return $this;
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

    public function getLogoFilename(): ?string
    {
        return $this->logo_filename;
    }

    public function setLogoFilename(string $logo_filename): self
    {
        $this->logo_filename = $logo_filename;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|RoomConnection[]
     */
    public function getRoomConnections(): Collection
    {
        return $this->roomConnections;
    }

    public function addRoomConnection(RoomConnection $roomConnection): self
    {
        if (!$this->roomConnections->contains($roomConnection)) {
            $this->roomConnections[] = $roomConnection;
            $roomConnection->setRoom($this);
        }

        return $this;
    }

    public function removeRoomConnection(RoomConnection $roomConnection): self
    {
        if ($this->roomConnections->removeElement($roomConnection)) {
            // set the owning side to null (unless already changed)
            if ($roomConnection->getRoom() === $this) {
                $roomConnection->setRoom(null);
            }
        }

        return $this;
    }

    public function getCurrentQuestionNumber(): ?int
    {
        return $this->current_question_number;
    }

    public function setCurrentQuestionNumber(?int $current_question_number): self
    {
        $this->current_question_number = $current_question_number;

        return $this;
    }

    public function getNumberOfQuestions(): ?int
    {
        return $this->number_of_questions;
    }

    public function setNumberOfQuestions(?int $number_of_questions): self
    {
        $this->number_of_questions = $number_of_questions;

        return $this;
    }

    /**
     * @return Collection|RoomQuestion[]
     */
    public function getRoomQuestions(): Collection
    {
        return $this->roomQuestions;
    }

    public function addRoomQuestion(RoomQuestion $roomQuestion): self
    {
        if (!$this->roomQuestions->contains($roomQuestion)) {
            $this->roomQuestions[] = $roomQuestion;
            $roomQuestion->setRoom($this);
        }

        return $this;
    }

    public function removeRoomQuestion(RoomQuestion $roomQuestion): self
    {
        if ($this->roomQuestions->removeElement($roomQuestion)) {
            // set the owning side to null (unless already changed)
            if ($roomQuestion->getRoom() === $this) {
                $roomQuestion->setRoom(null);
            }
        }

        return $this;
    }
}
