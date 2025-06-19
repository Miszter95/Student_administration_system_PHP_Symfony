<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Custom repository for the Student entities
 */
/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Custom query for the name search
     * @param $criterion - Search criteria
     * @return int|mixed|string
     */
    public function findStudentByName($criterion){

        $qb = $this->createQueryBuilder('s');
        $qb
            ->where(
                $qb->expr()->like('s.name',':criterion')
            )
            ->setParameter('criterion', '%'.$criterion.'%');
        $qb->orderBy('s.name','ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Custom query for the filter according to study groups
     * @param $array - Array of filter criteria
     * @return int|mixed|string
     */
    public function filterStudents($array)
    {

        $qb = $this->createQueryBuilder('s');

        $qb
                ->leftJoin('s.study_groups', 'g')
                ->where(
                $qb->expr()->in('g',':array')
            )
                ->setParameter('array', $array);
        $qb->orderBy('s.name','ASC');

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Student[] Returns an array of Student objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
