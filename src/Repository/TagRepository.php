<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function add(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Tag[] Returns an array of Tag objects
     * Tag with name 'Autre' will be the last item
     */
    public function findWithCustomOrder(int $limit = null, int $offset = null): array
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(Tag::class, 't');
        
        //? SQL
        $sql = '
            SELECT *, 1 AS custom_order FROM tag WHERE name != "Autre"
            UNION ALL
            SELECT *, 2 AS custom_order FROM tag WHERE name = "Autre"
            ORDER BY custom_order, name
        ';

        $sql .= null !== $limit && $limit > 1 ? ' LIMIT :limit' : '';        
        $sql .= null !== $offset && $offset > 1 ? ' OFFSET :offset' : '';

        $query = $entityManager->createNativeQuery($sql, $rsm);

        if (null !== $limit && $limit > 1) {
            $query->setParameter('limit', $limit, ParameterType::INTEGER);
        }

        if (null !== $offset && $offset > 1) {
            $query->setParameter('offset', $offset, ParameterType::INTEGER);
        }

        return $query->getResult();
    }

//    /**
//     * @return Tag[] Returns an array of Tag objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tag
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
