<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ServiceEntityRepository<Personne>
 *
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

//    /**
//     * @return Personne[] Returns an array of Personne objects
//     */
    public function findPersonnesByAgeInterval($ageMin, $ageMax): array
    {
        $qb =  $this->createQueryBuilder('p');
        $qb =  $this->createQueryBuilder('p');
        return $qb->getQuery()->getResult();
    }
    public function statsPersonnesByAgeInterval($ageMin, $ageMax): array
    {
        $qb =  $this->createQueryBuilder('p')
            ->select('avg(p.age) as ageMoyen, count(p.id) as nombrePersonne');
        $this->addIntervalAge($qb, $ageMin, $ageMax);
        return $qb->getQuery()->getScalarResult();
    }
    private function addIntervalAge(QueryBuilder $qb, $ageMin, $ageMax)
    {
        $qb->andWhere('p.age >= :ageMin AND p.age <= :ageMax')
            ->setParameter('ageMin', $ageMin)
            ->setParameter('ageMax', $ageMax);
    }
    public function searchByName($name, $page = 1, $limit = 10){
        $query = $this->createQueryBuilder('p')
                      ->andwhere('p.firstname LIKE: :name')
                      ->setParameter('name','%'.$name.'%')
                      ->getQuery();


    }

//    public function findOneBySomeField($value): ?Personne
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
