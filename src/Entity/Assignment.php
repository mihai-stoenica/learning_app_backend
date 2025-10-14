<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment extends Post
{
    #[ORM\Column(nullable: true)]
    #[Groups(['course_page', 'todo'])]
    private ?\DateTime $deadline = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Groups(['course_page', 'todo', 'work'])]
    private ?string $max_score = null;

    /**
     * @var Collection<int, Submission>
     */
    #[ORM\OneToMany(targetEntity: Submission::class, mappedBy: 'assignment')]
    private Collection $userAssignments;

    public function __construct()
    {
        parent::__construct();
        $this->userAssignments = new ArrayCollection();
    }

    public function getDeadline(): string
    {
        return $this->deadline->format("Y-m-d H:i:s");
    }

    public function getRawDeadline(): \DateTime
    {
        return $this->deadline;
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

    #[Groups(['course_page', 'todo'])]
    public function getType(): string
    {
        return "assignment";
    }

    /**
     * @return Collection<int, Submission>
     */
    public function getUserAssignments(): Collection
    {
        return $this->userAssignments;
    }

    public function addUserAssignment(Submission $userAssignment): static
    {
        if (!$this->userAssignments->contains($userAssignment)) {
            $this->userAssignments->add($userAssignment);
            $userAssignment->setAssignment($this);
        }

        return $this;
    }

    public function removeUserAssignment(Submission $userAssignment): static
    {
        if ($this->userAssignments->removeElement($userAssignment)) {
            if ($userAssignment->getAssignment() === $this) {
                $userAssignment->setAssignment(null);
            }
        }

        return $this;
    }

}
