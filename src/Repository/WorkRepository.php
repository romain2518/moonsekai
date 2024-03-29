<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Work;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Work>
 *
 * @method Work|null find($id, $lockMode = null, $lockVersion = null)
 * @method Work|null findOneBy(array $criteria, array $orderBy = null)
 * @method Work[]    findAll()
 * @method Work[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Work::class);
    }

    public function add(Work $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Work $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Work[] Returns an array of Work objects
     */
    public function findByFollower(User $user, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere(':follower MEMBER OF w.followers')
            ->setParameter('follower', $user)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneEagerMode(int $id): ?Work
    {
        return $this->createQueryBuilder('w')
            ->addSelect('movies, lightNovels, workNews, mangas, animes, tags, platforms')
            ->innerJoin('w.movies'      , 'movies')
            ->innerJoin('w.lightNovels' , 'lightNovels')
            ->innerJoin('w.workNews'    , 'workNews')
            ->innerJoin('w.mangas'      , 'mangas')
            ->innerJoin('w.animes'      , 'animes')
            ->innerJoin('w.tags'        , 'tags')
            ->innerJoin('w.platforms'   , 'platforms')
            ->andWhere('w.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return Work[] Returns an array of Work objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Work
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
