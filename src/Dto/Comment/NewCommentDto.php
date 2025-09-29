<?php

namespace App\Dto\Comment;

use App\Entity\Post;
use Symfony\Component\Validator\Constraints as Assert;

class NewCommentDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $text,

        #[Assert\NotBlank]
        public readonly int $post_id,

    ) {
    }
}
