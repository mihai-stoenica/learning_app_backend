<?php

namespace App\Entity;

use App\Repository\UserAssignmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserAssignmentRepository::class)]
#[ORM\Table(name: 'user_assignment_submission')]
class Submission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['submission_details'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Assignment $assignment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['submission_details'])]
    private ?string $answer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): static
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }
}
