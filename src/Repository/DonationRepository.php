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

    /**
     * renvoit la liste de don trié sur une catégorie
     * @return Donation[] Returns an array of Donation objects
     */
    
    public function findFilteredDonationWithProducts($category)
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
            ->join('p.category', 'c')
            ->addSelect('c')
            ->andwhere('c.name = :category')
            ->setParameter('category', $category)
            ->orderBy('d.created_at', 'DESC');

        return $query->getQuery()->getResult();
    }

        /**
     * @return Donation[] Returns an array of Donation objects
     */
    
    public function findDonationsByStatus($user)
    {
        $query = $this->createQueryBuilder('d')
            ->join('d.users','u')
            ->where('u.id = :user')
            ->setParameter('user', $user)
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


    
    public function findDonationWithAllDetails($value)
    {
         return $this->createQueryBuilder('d')
            ->where('d.id = :val')
            ->setParameter('val', $value)
            ->join('d.address', 'a')
            ->addSelect('a')
            ->andwhere('a.id = d.address')
            ->join('d.products', 'p')
            ->addSelect('p')
            ->join('d.status', 's')
            ->addSelect('s')
            ->andWhere('s.id = d.status')
            ->getQuery()
            ->getOneOrNullResult()
        ;
        
    }
}
