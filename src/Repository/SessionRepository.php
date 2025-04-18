<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function resetAllStartAndEndTimes(): void
    {
        $qb = $this->createQueryBuilder('s');
        $qb->update()
            ->set('s.start', 'null')
            ->set('s.stop', 'null')
            ->getQuery()
            ->execute();
    }

    /**
     * @param User $user
     * @return Session[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.organization', 'o')
            ->andWhere('o.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param bool $excludeCancelled
     * @return Session[]
     */
    public function findFullyAccepted($excludeCancelled = false): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->addSelect('sad')
            ->innerJoin('s.organization', 'o')
            ->addSelect('o')
            ->innerJoin('o.acceptedOrganizationDetails', 'oad')
            ->addSelect('oad')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->orderBy('s.start', 'ASC');

        if ($excludeCancelled) {
            $qb->andWhere('s.cancelled = FALSE');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Session[]
     */
    public function findAllWithProposedDetails(bool $hasChanges, bool $notApproved): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.proposedDetails', 'sdp')
            ->addSelect('sdp')
            ->orderBy('s.start');

        if ($hasChanges) {
            $qb->andWhere('s.proposedDetails != s.acceptedDetails');
        }

        if ($notApproved) {
            $qb->andWhere('s.acceptedDetails IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Session[]
     */
    public function findJuryAcceptedWithDetails(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->addSelect('sad')
            ->andWhere('s.cancelled = FALSE');
        // FIXME filter jury accept

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Session[]
     */
    public function findRecentlyApprovedSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->addSelect('sad')
            ->innerJoin('s.organization', 'o')
            ->addSelect('o')
            ->innerJoin('o.acceptedOrganizationDetails', 'oad')
            ->addSelect('oad')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->andWhere('s.acceptedAt IS NOT NULL')
            ->andWhere('s.start > CURRENT_TIMESTAMP()')
            ->andWhere('s.cancelled = FALSE')
            ->orderBy('s.acceptedAt', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function countSessions($onlineOnly = null, bool $cancelled = false)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('count(1)')
            ->innerJoin('s.organization', 'o')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->andWhere('s.cancelled = :cancelled')
            ->setParameter('cancelled', $cancelled);

        if ($onlineOnly !== null) {
            $qb->innerJoin('s.acceptedDetails', 'sad')
                ->andWhere('sad.onlineOnly = :onlineOnly')
                ->setParameter('onlineOnly', $onlineOnly);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countSessionsByDate()
    {
        $qb = $this->createQueryBuilder('s')
            ->select([
                "DATE_FORMAT(s.start, '%d.%m.%Y') AS date",
                'COUNT(NULLIF(s.cancelled, TRUE)) AS num',
                'COUNT(NULLIF(s.cancelled, FALSE)) AS num_cancelled',
            ])
            ->innerJoin('s.organization', 'o')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->groupBy('date');

        return $qb->getQuery()->getScalarResult();
    }

    public function countSessionsByChannel()
    {
        $qb = $this->createQueryBuilder('s')
            ->select([
                'c.name AS channel',
                'COUNT(NULLIF(s.cancelled, TRUE)) AS num',
                'COUNT(NULLIF(s.cancelled, FALSE)) AS num_cancelled',
            ])
            ->innerJoin('s.organization', 'o')
            ->innerJoin('s.acceptedDetails', 'sad')
            ->innerJoin('sad.channel', 'c')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->groupBy('channel')
            ->orderBy('channel');

        return $qb->getQuery()->getScalarResult();
    }

    public function countParallelSession(\DateTimeInterface $start, \DateTimeInterface $end, ?int $excludeId)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(1)')
            ->innerJoin('s.organization', 'o')
            ->andWhere('s.start IS NOT NULL')
            ->andWhere('s.acceptedDetails IS NOT NULL')
            ->andWhere('o.acceptedOrganizationDetails IS NOT NULL')
            ->andWhere('s.cancelled = FALSE');

        $stop = "COALESCE( s.stop, DATE_ADD(s.start, 2, 'hour') )";

        $qb->andWhere(
            $qb
                ->expr()
                ->orX(
                    $qb->expr()->andX($qb->expr()->lte('s.start', ':start'), $qb->expr()->gt($stop, ':start')),
                    $qb->expr()->andX($qb->expr()->lt('s.start', ':end'), $qb->expr()->gte($stop, ':end')),
                    $qb->expr()->andX($qb->expr()->gte('s.start', ':start'), $qb->expr()->lte($stop, ':end'))
                )
        )
            ->setParameter(':start', $start)
            ->setParameter(':end', $end);

        if ($excludeId !== null) {
            $qb->andWhere('s.id != :excludeId')->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
