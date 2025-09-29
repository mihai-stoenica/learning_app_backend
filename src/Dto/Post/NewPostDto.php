<?php

namespace App\Dto\Post;

use Symfony\Component\Validator\Constraints as Assert;

class NewPostDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $text,

        #[Assert\NotBlank]
        public readonly string $title,


    ) {
    }
}
