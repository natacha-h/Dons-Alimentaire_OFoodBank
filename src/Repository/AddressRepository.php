<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Address::class);
    }

    // /**
    //  * @return Address[] Returns an array of Address objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Address
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // public function findByAllFields($currentNumber, $currentStreet1, $currentStreet2, $currentZipCode, $currentCity): ?Address
    // {
    //     return $this->createQueryBuilder('a')
    //         ->where('a.number = :number')
    //         ->andWhere('a.street_1 = :street1')
    //         ->andWhere('a.street_2 = :street2')
    //         ->andWhere('a.zip_code = :zip_code')
    //         ->andWhere('a.city = :city')
    //         ->setParameters([
    //             'number' => $currentNumber,
    //             'street1' => $currentStreet1,
    //             'street2' => $currentStreet2,
    //             'zip_code' => $currentZipCode,
    //             'city' => $currentCity,
    //         ])
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
