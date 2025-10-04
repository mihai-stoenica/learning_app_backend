<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\User;
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
    #[IsGranted('submit','assignment', "You have to be a student to submit an assignment")]
    public function new(Assignment $assignment,  Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
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

}
