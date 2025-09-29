<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

//    /**
//     * @return Course[] Returns an array of Course objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Course
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getMyCourses(User $user): array {
        return $this->createQueryBuilder('c')
            ->join('c.teachers', 't')
            ->where('t = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function getEnrolledCourses(User $user): array
    {
        $teachingCourses = $this->createQueryBuilder('c')
            ->join('c.teachers', 't')
            ->where('t = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $studentCourses = $this->createQueryBuilder('c')
            ->join('c.students', 's')
            ->where('s = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $allCourses = [];
        foreach ($teachingCourses as $teachingCourse) {
            array_push($allCourses, $teachingCourse);
        }
        foreach ($studentCourses as $studentCourse) {
            array_push($allCourses, $studentCourse);
        }
        return $allCourses;
    }
}
