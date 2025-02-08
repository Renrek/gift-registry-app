<?php

namespace App\Repository;

use App\Entity\GiftRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftRequest>
 *
 * @method GiftRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftRequest|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method GiftRequest[]    findAll()
 * @method GiftRequest[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
class GiftRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftRequest::class);
    }

    /**
     * Finds an entity by its primary key or throws an EntityNotFoundException.
     *
     * @param int $id
     * @return GiftRequest
     * @throws EntityNotFoundException
     */
    public function findOrFail(int $id): GiftRequest
    {
        $giftRequest = $this->find($id);
        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }
        return $giftRequest;
    }

    //    /**
    //     * @return GiftRequest[] Returns an array of GiftRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GiftRequest
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
