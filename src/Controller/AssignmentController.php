<?php

namespace App\Controller;

use App\Dto\Assignment\NewAssignmentDto;
use App\Entity\Assignment;
use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/assignment')]
#[IsGranted('edit', 'course',  "You do not have access.")]
final class AssignmentController extends AbstractController
{
    #[Route('/new/course/{id}', name: 'app_assignment_new', methods: ['POST'])]
    public function new(#[MapRequestPayload] NewAssignmentDto $assignmentDto, Course $course, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $assignment = new Assignment();
        $assignment->setCourse($course);
        $assignment->setUser($this->getUser());
        $assignment->setText($assignmentDto->text);
        $assignment->setTitle($assignmentDto->title);
        $assignment->setDeadline(
            $assignmentDto->deadline ? \DateTime::createFromInterface($assignmentDto->deadline) : null
        );
        $assignment->setMaxScore($assignmentDto->max_score);

        $entityManager->persist($assignment);
        $entityManager->flush();

        $json = $serializer->serialize($assignment, 'json', ['groups' => ['course_page']]);

        return new JsonResponse($json, Response::HTTP_CREATED);

    }
}
