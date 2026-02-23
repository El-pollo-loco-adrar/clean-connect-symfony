<?php

namespace App\Repository;

use App\Entity\Candidate;
use App\Entity\Conversation;
use App\Entity\Employer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findLastMessage(Candidate|Employer $user): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.messages', 'm')
            ->andWhere('c.candidate = :user OR c.employer = :user')
            ->setParameter('user', $user)
            ->groupBy('c.id')
            ->orderBy('MAX(m.createdAt)', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
