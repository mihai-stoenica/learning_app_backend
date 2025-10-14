<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findByCourse(Course $course, int $beforeId = null, int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.course = :course')
            ->setParameter('course', $course)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit);

        if ($beforeId !== null) {
            $qb->andWhere('m.id < :beforeId')
                ->setParameter('beforeId', $beforeId);
        }
        return $qb->getQuery()->getResult();
    }
}
