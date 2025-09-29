<?php

namespace App\Dto\Course;

use Symfony\Component\Validator\Constraints as Assert;

class NewCourseDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly string $description,

    ) {
    }
}
