<?php

namespace App\Repository;


use App\Entity\Intervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Property;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

/**
 * @extends ServiceEntityRepository<Intervention>
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    public function countByOwnerSince(User $owner, \DateTimeImmutable $since): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->join('i.property', 'p')
            ->andWhere('p.owner = :owner')
            ->andWhere('i.businessDate >= :since')
            ->setParameter('owner', $owner)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countNonConformByOwnerSince(User $owner, \DateTimeImmutable $since): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->join('i.property', 'p')
            ->andWhere('p.owner = :owner')
            ->andWhere('i.businessDate >= :since')
            ->andWhere('
            i.checkBedMade = false
            OR i.checkFloorClean = false
            OR i.checkBathroomOk = false
            OR i.checkKitchenOk = false
            OR i.checkLinenChanged = false
        ')
            ->setParameter('owner', $owner)
            ->setParameter('since', $since)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Property[]|Collection $properties
     * @return array<int, Intervention>  propertyId => last Intervention
     */
    public function findLastByProperties(iterable $properties): array
    {
        if (empty($properties)) {
            return [];
        }

        $ids = [];
        foreach ($properties as $property) {
            $ids[] = $property->getId();
        }

        $rows = $this->createQueryBuilder('i')
            ->join('i.property', 'p')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('i.businessDate', 'DESC')
            ->getQuery()
            ->getResult();

        $map = [];
        foreach ($rows as $intervention) {
            $pid = $intervention->getProperty()->getId();
            if (!isset($map[$pid])) {
                $map[$pid] = $intervention;
            }
        }

        return $map;
    }

    public function findOneByPropertyAndBusinessDate(Property $property, \DateTimeImmutable $businessDate): ?Intervention
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.property = :property')
            ->andWhere('i.businessDate = :date')
            ->setParameter('property', $property)
            ->setParameter('date', $businessDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneForOwner(int $id, \App\Entity\User $owner): ?\App\Entity\Intervention
    {
        return $this->createQueryBuilder('i')
            ->addSelect('p', 'w', 'ph')
            ->join('i.property', 'p')
            ->join('i.createdBy', 'w')
            ->leftJoin('i.photos', 'ph')
            ->andWhere('i.id = :id')
            ->andWhere('p.owner = :owner')
            ->setParameter('id', $id)
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Intervention[] Returns an array of Intervention objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Intervention
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
