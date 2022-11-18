<?php

namespace App\Repository;

use App\Entity\CalendarEvent;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarEvent>
 *
 * @method CalendarEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalendarEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalendarEvent[]    findAll()
 * @method CalendarEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarEvent::class);
    }

    public function add(CalendarEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CalendarEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return CalendarEvent[] Returns an array of CalendarEvent objects with properties target name & work id filled
     */
    public function findAllWithTarget(User $user = null, int $limit = null, int $offset = null): array
    {
        $conn = $this->getEntityManager()->getConnection();

        //? SQL
        $sql = '
            SELECT * FROM (
                SELECT
                    start, title,
                    (SELECT
                        CASE
                            WHEN name is not null THEN CONCAT(number, " : ", name)
                            ELSE number
                        END
                    FROM chapter WHERE id = target_id) AS target_name,
                    (SELECT w.id FROM chapter c
                    INNER JOIN volume v ON volume_id = v.id
                    INNER JOIN manga m ON manga_id = m.id
                    INNER JOIN work w ON work_id = w.id
                    WHERE c.id = target_id) AS work_id,
                    (SELECT
                        CASE
                            WHEN v.picture_path is not null THEN CONCAT("volumePictures/", v.picture_path)
                            ELSE CONCAT("mangaPictures/", m.picture_path)
                        END
                    FROM chapter c
                    INNER JOIN volume v ON volume_id = v.id
                    INNER JOIN manga m ON manga_id = m.id
                    WHERE c.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Chapter"
                UNION ALL
                SELECT
                    start, title,
                    (SELECT
                        CASE
                            WHEN name is not null THEN CONCAT(number, " : ", name)
                            ELSE number
                        END
                    FROM episode WHERE id = target_id) AS target_name, 
                    (SELECT w.id FROM episode e
                    INNER JOIN season s ON season_id = s.id
                    INNER JOIN anime a ON anime_id = a.id
                    INNER JOIN work w ON work_id = w.id
                    WHERE e.id = target_id) AS work_id,
                    (SELECT
                        CASE
                            WHEN s.picture_path is not null THEN CONCAT("seasonPictures/", s.picture_path)
                            ELSE CONCAT("animePictures/", a.picture_path)
                        END
                    FROM episode e
                    INNER JOIN season s ON season_id = s.id
                    INNER JOIN anime a ON anime_id = a.id
                    WHERE e.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Episode"
                UNION ALL
                SELECT
                    start, title,
                    (SELECT name FROM light_novel WHERE id = target_id) AS target_name, 
                    (SELECT w.id FROM light_novel l
                    INNER JOIN work w ON work_id = w.id
                    WHERE l.id = target_id) AS work_id,
                    (SELECT CONCAT("lightNovelPictures/", l.picture_path) FROM light_novel l
                    WHERE l.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\LightNovel"
                UNION ALL
                SELECT
                    start, title,
                    (SELECT name FROM movie WHERE id = target_id) AS target_name, 
                    (SELECT w.id FROM movie m
                    INNER JOIN work w ON work_id = w.id
                    WHERE m.id = target_id) AS work_id,
                    (SELECT CONCAT("moviePictures/", m.picture_path) FROM movie m
                    WHERE m.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Movie"
                UNION ALL
                SELECT
                    start, title,
                    (SELECT title FROM news WHERE id = target_id) AS target_name, 
                    (SELECT null) AS work_id,
                    (SELECT
                        CASE
                            WHEN n.picture_path is not null THEN CONCAT("newsPictures/", n.picture_path)
                            ELSE null
                        END
                    FROM news n
                    WHERE n.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\News"
                UNION ALL
                SELECT
                    start, title,
                    (SELECT title FROM work_news WHERE id = target_id) AS target_name, 
                    (SELECT w.id FROM work_news wn
                    INNER JOIN work w ON work_id = w.id
                    WHERE wn.id = target_id) AS work_id,
                    (SELECT
                        CASE
                            WHEN wn.picture_path is not null THEN CONCAT("workNewsPictures/", wn.picture_path)
                            ELSE CONCAT("workPictures/", w.picture_path)
                        END
                    FROM work_news wn
                    INNER JOIN work w ON work_id = w.id
                    WHERE wn.id = target_id) AS picture_path
                FROM calendar_event WHERE target_table = "App\\\Entity\\\WorkNews"
                ) AS q
        ';

        $sql .= null !== $user ? ' WHERE work_id IN (SELECT wu.work_id FROM work_user wu WHERE wu.user_id = :user_id)' : '';

        $sql .= ' ORDER BY start, title';
        
        $sql .= null !== $limit && $limit > 1 ? ' LIMIT :limit' : '';        
        $sql .= null !== $offset && $offset > 1 ? ' OFFSET :offset' : '';

        //? Query
        $stmt = $conn->prepare($sql);
        
        if (null !== $limit && $limit > 1) {
            $stmt->bindValue('limit', $limit, ParameterType::INTEGER);
        }

        if (null !== $offset && $offset > 1) {
            $stmt->bindValue('offset', $offset, ParameterType::INTEGER);
        }

        if (null !== $user) {
            $stmt->bindValue('user_id', $user->getId(), ParameterType::INTEGER);
        }

        $resultSet = $stmt->executeQuery();

        $arrayResults = $resultSet->fetchAllAssociative();

        //? Adding target names to objects
        if (null !== $user) {
            $calendarEvents = $this->findByFollower($user, $limit, $offset);
        } else {
            $calendarEvents = $this->findBy([], ['start' => 'ASC', 'title' => 'ASC'], $limit, $offset);
        }

        foreach ($calendarEvents as $key => $calendarEvent) {
            $calendarEvent->setTargetName($arrayResults[$key]['target_name']);
            $calendarEvent->setWorkId($arrayResults[$key]['work_id']);
            $calendarEvent->setPicturePath($arrayResults[$key]['picture_path']);
        }

        return $calendarEvents;
    }

    /**
     * @return CalendarEvent[] Returns an array of the CalendarEvent objects rerlated to works followed by the specified user
     */
    private function findByFollower(User $user, int $limit = null, int $offset = null)
    {
        $entityManager = $this->getEntityManager();
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata(CalendarEvent::class, 'c');

        //? SQL
        $sql = '
            SELECT * FROM (
                SELECT
                    *,
                    (SELECT w.id FROM chapter c
                    INNER JOIN volume v ON volume_id = v.id
                    INNER JOIN manga m ON manga_id = m.id
                    INNER JOIN work w ON work_id = w.id
                    WHERE c.id = target_id) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Chapter"
                UNION ALL
                SELECT
                    *,
                    (SELECT w.id FROM episode e
                    INNER JOIN season s ON season_id = s.id
                    INNER JOIN anime a ON anime_id = a.id
                    INNER JOIN work w ON work_id = w.id
                    WHERE e.id = target_id) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Episode"
                UNION ALL
                SELECT
                    *,
                    (SELECT w.id FROM light_novel l
                    INNER JOIN work w ON work_id = w.id
                    WHERE l.id = target_id) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\LightNovel"
                UNION ALL
                SELECT
                    *,
                    (SELECT w.id FROM movie m
                    INNER JOIN work w ON work_id = w.id
                    WHERE m.id = target_id) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\Movie"
                UNION ALL
                SELECT
                    *,
                    (SELECT null) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\News"
                UNION ALL
                SELECT
                    *,
                    (SELECT w.id FROM work_news wn
                    INNER JOIN work w ON work_id = wn.id
                    WHERE wn.id = target_id) AS work_id
                FROM calendar_event WHERE target_table = "App\\\Entity\\\WorkNews"
                ) AS q
            WHERE work_id IN (SELECT wu.work_id FROM work_user wu WHERE wu.user_id = :user_id)
            ORDER BY start, title
        ';

        $sql .= null !== $limit && $limit > 1 ? ' LIMIT :limit' : '';        
        $sql .= null !== $offset && $offset > 1 ? ' OFFSET :offset' : '';

        $query = $entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('user_id', $user->getId(), ParameterType::INTEGER);

        if (null !== $limit && $limit > 1) {
            $query->setParameter('limit', $limit, ParameterType::INTEGER);
        }

        if (null !== $offset && $offset > 1) {
            $query->setParameter('offset', $offset, ParameterType::INTEGER);
        }

        return $query->getResult();
    }

//    /**
//     * @return CalendarEvent[] Returns an array of CalendarEvent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CalendarEvent
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
