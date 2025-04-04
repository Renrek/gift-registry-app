<?php

namespace App\Repository;

use App\Entity\GiftClaim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftClaim>
 *
 * @method GiftClaim|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftClaim|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method GiftClaim[]    findAll()
 * @method GiftClaim[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class GiftClaimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftClaim::class);
    }

    /**
     * Finds all claims for a specific gift request.
     *
     * @param int $giftRequestId
     * @return GiftClaim[]
     */
    public function findByGiftRequestId(int $giftRequestId): array
    {
        return $this->createQueryBuilder('gc')
            ->andWhere('gc.giftRequest = :giftRequestId')
            ->setParameter('giftRequestId', $giftRequestId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Calculates the total claimed quantity for a specific gift request.
     *
     * @param int $giftRequestId
     * @return int
     */
    public function getTotalClaimedQuantity(int $giftRequestId): int
    {
        return (int) $this->createQueryBuilder('gc')
            ->select('SUM(gc.quantity)')
            ->andWhere('gc.giftRequest = :giftRequestId')
            ->setParameter('giftRequestId', $giftRequestId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}