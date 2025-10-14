<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;;

use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/chat')]
final class ChatController extends AbstractController
{
    #[Route('/send/{course}', name: 'app_chat')]
    #[IsGranted('view','course', 'You have to be a member of this course.')]
    public function send(Course $course, HubInterface $hub, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $messageText = $request->getPayload()->get('message');

        $message = new Message();
        $message->setCourse($course);
        $message->setUser($this->getUser());
        $message->setText($messageText);
        $message->setDate(new \DateTime('now'));

        $entityManager->persist($message);
        $entityManager->flush();

        $jsonMessage = $serializer->serialize($message, 'json', ['groups' => ['message']]);

        $data = [
            'message' => $jsonMessage,
        ];

        $update = new Update(
            "http://localhost/course/{$course->getId()}/chat",
            json_encode($data),
            true
        );

        $hub->publish($update);

        return $this->json([
            'status' => 'ok',
            'topic' => "http://localhost/course/{$course->getId()}/chat",
            'data' => $data,
        ]);
    }

    #[Route('/subscribe-jwt', name: 'mercure_subscribe_jwt', methods: ['GET'])]
    public function getSubscribeJwt(TokenProviderInterface $tokenProvider): JsonResponse
    {
        $jwt = $tokenProvider->getJwt();

        $response = new JsonResponse(['success' => true]);

        $response->headers->setCookie(
            Cookie::create('mercureAuthorization', $jwt)
                ->withSecure(false)
                ->withHttpOnly(true)
                ->withSameSite('lax')
                ->withPath('/.well-known/mercure')
        );

        return $response;
    }
}
