<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment extends Post
{
    #[ORM\Column(nullable: true)]
    #[Groups('course_page')]
    private ?\DateTime $deadline = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Groups('course_page')]
    private ?string $max_score = null;

    public function getDeadline(): string
    {
        return $this->deadline->format("Y-m-d H:i:s");
    }

    public function setDeadline(?\DateTime $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getMaxScore(): ?string
    {
        return $this->max_score;
    }

    public function setMaxScore(string $max_score): static
    {
        $this->max_score = $max_score;

        return $this;
    }

    #[Groups(['course_page'])]
    public function getType(): string
    {
        return "assignment";
    }
}
