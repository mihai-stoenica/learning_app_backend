<?php

namespace App\Dto\Assignment;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class NewAssignmentDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $title,

        #[Assert\NotBlank]
        public readonly string $text,

        #[Assert\NotBlank]
        #[Assert\NotNull]
        #[Assert\Type(\DateTimeInterface::class)]
        #[GreaterThanOrEqual('now')]
        public readonly \DateTimeInterface $deadline,

        #[Assert\NotBlank]
        #[Assert\GreaterThan(0)]
        public readonly float $max_score,
    ) {
    }
}
