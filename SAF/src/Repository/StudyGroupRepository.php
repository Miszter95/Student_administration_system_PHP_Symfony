<?php

namespace App\Repository;

use App\Entity\StudyGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * Custom repository for the StudyGroup entities
 */
/**
 * @method StudyGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudyGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudyGroup[]    findAll()
 * @method StudyGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudyGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudyGroup::class);
    }

    /**
     * Custom query for the group name search
     * @param $criterion - Search criteria
     * @return int|mixed|string
     */
    public function findStudyGroupByName($criterion){

        $qb = $this->createQueryBuilder('sg');
        $qb
            ->where(
                $qb->expr()->like('sg.group_name',':criterion')
            )
            ->setParameter('criterion', '%'.$criterion.'%');
        $qb->orderBy('sg.group_name','ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Custom query for the filter according to enrolled students
     * @param $array - Array of filter criteria
     * @return int|mixed|string
     */
    public function filterSGroups($array)
    {

        $qb = $this->createQueryBuilder('sg');

        $qb
            ->leftJoin('sg.enrolled_students', 's')
            ->where(
                $qb->expr()->in('s',':array')
            )
            ->setParameter('array', $array);
        $qb->orderBy('sg.group_name','ASC');

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return StudyGroup[] Returns an array of StudyGroup objects
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
    public function findOneBySomeField($value): ?StudyGroup
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
