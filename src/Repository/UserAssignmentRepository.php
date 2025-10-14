<?php

namespace App\Repository;

use App\Entity\Submission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Submission>
 */
class UserAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Submission::class);
    }

    public function hasUserSubmittedAssignment($user, $post) : bool
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->andWhere('u.assignment = :assignment')
            ->setParameter('user', $user)
            ->setParameter('assignment', $post)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function getByCourse($course) : array
    {
        return $this->createQueryBuilder('s')
            ->join('s.assignment', 'a')
            ->join('a.course', 'c')
            ->andWhere('c = :course')
            ->setParameter('course', $course)
            ->getQuery()
            ->getResult();

    }
}
