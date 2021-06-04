<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=RoomQuestion::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room_question;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=RoomConnection::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $answer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomQuestion(): ?RoomQuestion
    {
        return $this->room_question;
    }

    public function setRoomQuestion(?RoomQuestion $room_question): self
    {
        $this->room_question = $room_question;

        return $this;
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

    public function getAnswer(): ?RoomConnection
    {
        return $this->answer;
    }

    public function setAnswer(?RoomConnection $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
