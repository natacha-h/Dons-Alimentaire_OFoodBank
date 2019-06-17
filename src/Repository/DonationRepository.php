<?php

namespace App\Repository;

use App\Entity\Donation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Donation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donation[]    findAll()
 * @method Donation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Donation::class);
    }

    /**
     * @return Donation[] Returns an array of Donation objects
     */
    
    public function findByStatusQuery()
    {
        $query = $this->createQueryBuilder('d')
            ->join('d.status', 's')
            ->addSelect('s')
            ->where('s.name = :status')
            ->orWhere('s.name = :status2')
            ->setParameters([
                'status' => 'Dispo',
                'status2' => 'Réservé'
            ])
            ->orderBy('d.created_at', 'DESC');

        return $query->getQuery();
    }

    /**
     * @return Donation[] Returns an array of Donation objects
     */
    
    public function findDonationWithProducts()
    {
        $query = $this->createQueryBuilder('d')
            ->join('d.status', 's')
            ->addSelect('s')
            ->where('s.name = :status')
            ->orWhere('s.name = :status2')
            ->setParameters([
                'status' => 'Dispo',
                'status2' => 'Réservé'
            ])
            ->join('d.products', 'p')
            ->addSelect('p')
            ->orderBy('d.created_at', 'DESC');

        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Donation
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /*
        public function findDonationByStatus($value,)
            {
                return $this->createQueryBuilder('d')
                    ->andWhere('d.status = :val')
                    ->setParameter('val', $value)
                    ->getQuery()
                    ->getResult()
                ;
            }
    */

}
