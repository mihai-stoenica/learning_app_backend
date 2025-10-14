<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Submission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/submission')]
final class SubmissionController extends AbstractController
{
    #[Route('/new/assignment/{assignment}', name: 'app_user_assignment_submission_new', methods: ['POST'])]
    #[IsGranted('submit', 'assignment', "You have to be a student to submit an assignment")]
    public function new(Assignment $assignment, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        if ($assignment->getRawDeadline() < new \DateTime('now', timezone: new \DateTimeZone('Europe/Bucharest'))) {
            return new JsonResponse(['message' => "You can't submit an answer after the deadline."], Response::HTTP_BAD_REQUEST);
        }

        $answerUrl = $request->getPayload()->get('answer');
        $user = $this->getUser();
        $submission = new Submission();
        $submission->setAssignment($assignment);
        $submission->setUser($user);
        $submission->setAnswer($answerUrl);

        $entityManager->persist($submission);
        $entityManager->flush();

        $json = $serializer->serialize($submission, 'json', ['groups' => 'submission_details']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/score/submission/{submission}', name: 'app_user_assignment_submission_score', methods: ['POST'])]
    #[IsGranted('score','submission', "You have to be a teacher to score someone's work.")]
    public function score(Submission $submission, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $score = $request->getPayload()->get('score');
        $maxScore = $submission->getAssignment()->getMaxScore();

        if($score > $maxScore || $score < 0) {
            return new JsonResponse(["message" => "The score has to be between 0 and max score."], Response::HTTP_BAD_REQUEST);
        }

        $submission->setScore($score);
        $entityManager->persist($submission);
        $entityManager->flush();

        $json = $serializer->serialize($submission, 'json', ['groups' => 'work']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
