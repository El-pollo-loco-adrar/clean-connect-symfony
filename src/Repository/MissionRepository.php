<?php

namespace App\Repository;

use App\Entity\Candidate;
use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    /**
     * Récupère les missions qui correspondent aux zones d'intervention du candidat
     */
    public function findMissionsByCandidateAreas(Candidate $candidate)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.areaLocation', 'a')
            ->innerJoin('a.candidates', 'c')
            ->where('c.id = :candidateId')
            ->andWhere('m.startAt > :now')
            ->setParameter('candidateId', $candidate->getId())
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('m.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
