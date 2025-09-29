<?php

namespace App\Controller;

use App\Dto\Comment\NewCommentDto;
use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/comment')]
final class CommentController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post_comments', methods: ['GET'])]
    public function post_comments(Post $post, SerializerInterface $serializer, PostRepository $postRepository): Response
    {
        $comments = $post->getComments();
        $json = $serializer->serialize($comments, 'json',['groups' => 'post_page']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'app_post_new_comment', methods: ['POST'])]
    public function new(#[MapRequestPayload] NewCommentDto $commentDto, SerializerInterface $serializer, PostRepository $postRepository, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $post = $postRepository->find($commentDto->post_id);
        $comment->setPost($post);
        $comment->setText($commentDto->text);

        $entityManager->persist($comment);
        $entityManager->flush();

        $json = $serializer->serialize($comment, 'json',['groups' => 'post_page']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);

    }
}
