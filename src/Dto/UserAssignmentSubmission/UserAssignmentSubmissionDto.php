<?php

namespace App\Dto\UserAssignmentSubmission;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class UserAssignmentSubmissionDto
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $answer,
    ) {
    }
}
