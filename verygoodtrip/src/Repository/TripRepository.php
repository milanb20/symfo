<?php

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findByCriteria(int $country, Array $dates, float $price)
    {
      $query = $this->createQueryBuilder('t');

      if ($country != 0) {
        $query
          ->andWhere('t.country = :country')
          ->setParameter('country', $country);
      }

      if ($dates['start'] != null) {
        $query
          ->andWhere('t.date_start > :date_start')
          ->setParameter('date_start', $dates['start']);
      }

      if ($dates['end'] != null) {
        $query
          ->andWhere('t.date_end < :date_end')
          ->setParameter('date_end', $dates['end']);
      }

      if ($price != null) {
        $query
          ->andWhere('t.price <= :price')
          ->setParameter('price', $price);
      }

      return $query->getQuery()->getResult();
    }

//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
