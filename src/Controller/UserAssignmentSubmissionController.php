<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\User;
use App\Entity\UserAssignmentSubmission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user_assignment_submission')]
final class UserAssignmentSubmissionController extends AbstractController
{
    #[Route('/new/assignment/{assignment}', name: 'app_user_assignment_submission_new', methods: ['POST'])]
    public function new(Assignment $assignment,  Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $answerUrl = $request->getPayload()->get('answer');
        $userEmail = $request->getPayload()->get('userEmail');
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);
        $submission = new UserAssignmentSubmission();
        $submission->setAssignment($assignment);
        $submission->setUser($user);
        $submission->setAnswer($answerUrl);

        $entityManager->persist($submission);
        $entityManager->flush();

        $json = $serializer->serialize($submission, 'json', ['groups' => 'submission_details']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }
}
