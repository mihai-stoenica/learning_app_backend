<?php

namespace App\Controller;

use App\Dto\Post\NewPostDto;
use App\Entity\Assignment;
use App\Entity\Course;
use App\Entity\Post;
use App\Entity\UserAssignmentSubmission;
use App\Repository\UserAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/post')]
#[IsGranted('view', 'course',  "You do not have access.")]
final class PostController extends AbstractController
{
    #[Route('/course/{id}', name: 'app_post', methods: ['GET'])]
    public function index(Course $course, SerializerInterface $serializer, UserAssignmentRepository $submissionRepository): Response
    {
        $user = $this->getUser();
        $posts = $course->getPosts();
        $enrichedPosts = [];

        foreach ($posts as $post) {
            $postData = $serializer->normalize($post, null, ['groups' => ['course_page']]);

            if ($post instanceof Assignment) {
                $isSubmitted = $submissionRepository->hasUserSubmittedAssignment($user, $post);

                $enrichedPosts[] = [
                    ...$postData,
                    'isSubmitted' => $isSubmitted,
                ];
            } else {
                $enrichedPosts[] = $postData;
            }
        }

        $json = $serializer->serialize($enrichedPosts, 'json', ['groups' => ['course_page']]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/new/course/{id}', name: 'app_post_new', methods: ['POST'])]
    public function new(#[MapRequestPayload] NewPostDto $postDto, Course $course, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $post = new Post();
        $post->setText($postDto->text);
        $post->setTitle($postDto->title);
        $user = $this->getUser();
        $post->setUser($user);
        $post->setCourse($course);

        $entityManager->persist($post);
        $entityManager->flush();
        $json = $serializer->serialize($post, 'json', ['groups' => ['course_page']]);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);

    }
}
