<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/message')]
final class MessageController extends AbstractController
{
    #[Route('/course/{course}', name: 'app_message_course')]
    public function index(Course $course, SerializerInterface $serializer, MessageRepository $messageRepository, Request $request): Response
    {
        $beforeId = $request->query->get('beforeId');
        $beforeId = $beforeId !== null ? (int) $beforeId : null;

        $limit = (int) $request->query->get('limit', 20);

        $messages = $messageRepository->findByCourse($course, $beforeId, $limit);
        $jsonMessages = $serializer->serialize($messages, 'json', ['groups' => ['message']]);

        return new JsonResponse($jsonMessages, Response::HTTP_OK, [], true);
    }
}
