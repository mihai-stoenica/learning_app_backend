<?php

namespace App\Controller;

use App\Dto\Course\NewCourseDto;
use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use App\Service\CourseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/course')]
final class CourseController extends AbstractController
{
    #[Route('/', name: 'app_course', methods: ['GET'])]
    public function index(CourseRepository $courseRepository, SerializerInterface $serializer, Request $request): Response
    {
        $mine = $request->query->get('mine') === 'true';
        $user = $this->getUser();
        $mine ? $courses = $courseRepository->getMyCourses($user) : $courses = $courseRepository->getEnrolledCourses($user);

        $json = $serializer->serialize($courses, 'json', ['groups' => 'course_details']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'app_course_new', methods: ['POST'])]
    public function new(#[MapRequestPayload] NewCourseDto $courseDto, EntityManagerInterface $entityManager, SerializerInterface $serializer, CourseService $courseService): Response
    {

        $course = new Course();
        $user = $this->getUser();

        $course->setName($courseDto->name);
        $course->setDescription($courseDto->description);
        $course->addTeacher($user);

        $courseService->generateAccessCode($course);

        $entityManager->persist($course);
        $entityManager->flush();

        $json = $serializer->serialize($course, 'json', ['groups' => 'course_details']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'app_course_page', methods: ['GET'])]
    #[IsGranted('view', 'course', message: "You don't have access to this course")]
    public function page(Course $course, SerializerInterface $serializer, Request $request): Response
    {
        $json = $serializer->serialize($course, 'json', ['groups' => 'course_page']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/join', name: 'app_course_join', methods: ['POST'])]
    public function join_course(SerializerInterface $serializer, Request $request, CourseRepository $courseRepository, EntityManagerInterface $entityManager): Response
    {
        $data = $request->getPayload()->all();
        $accessCode = $data['access_code'] ?? null;
        $course = $courseRepository->findOneBy(['access_code' => $accessCode]);
        $user = $this->getUser();

        if($course->getStudents()->contains($user) || $course->getTeachers()->contains($user)){
            return new JsonResponse(
                ['message' => "You are already a member of this course"],
                Response::HTTP_FORBIDDEN
            );
        }
        $course->addStudent($user);

        $entityManager->persist($course);
        $entityManager->flush();

        $json = $serializer->serialize($course, 'json', ['groups' => 'course_details']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/make_teacher/{id}', name: 'app_course_join', methods: ['POST'])]
    #[IsGranted('edit', 'course', message: "You don't have access to do this.")]
    public function make_teacher(Course $course, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $userId = $request->getPayload()->get('user_id');
        $user = $userRepository->find($userId);

        if($course->getTeachers()->contains($user)){
            return new JsonResponse(['message' => 'This user is already a teacher of this course.'], Response::HTTP_FORBIDDEN);
        }

        if(!$course->getStudents()->contains($user)) {
            return new JsonResponse(['message' => 'This user is not enrolled in this course.'], Response::HTTP_FORBIDDEN);
        }

        $course->addTeacher($user);
        $course->removeStudent($user);

        $entityManager->persist($course);
        $entityManager->flush();

        $json = $serializer->serialize($course, 'json', ['groups' => 'course_details']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
