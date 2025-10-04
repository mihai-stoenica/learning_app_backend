<?php

namespace App\Repository;

use App\Entity\Assignment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function getTodo(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.course', 'c')
            ->join('c.students', 's')
            ->leftJoin('a.userAssignments', 'r', 'WITH', 'r.user = s') // join user submissions
            ->where('s = :user')
            ->andWhere('r.id IS NULL') // no submission yet
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
