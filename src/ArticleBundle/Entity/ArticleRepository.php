<?php
namespace ArticleBundle\Entity;

use Doctrine\ORM\QueryBuilder;
/**
 * ArticleRepository
 */
class ArticleRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findPublished() {
        $qb = $this->createQueryBuilder('a');
        $now = new \DateTime();

        return $qb
            ->where($qb->expr()->orX( // Where expires_at > now OR expires_at is null
                $qb->expr()->gt('a.expires_at', ':now1'),
                $qb->expr()->isNull('a.expires_at')
            ))
            ->andWhere(
                $qb->expr()->orX( // Where published_at < now OR published_at is null
                    $qb->expr()->lt('a.published_at', ':now2'),
                    $qb->expr()->isNull('a.published_at')
                )
            )
            ->andWhere('a.published = 1')
            ->setParameters(array('now1' => $now->format("Y-m-d H:i:s"), 'now2' => $now->format("Y-m-d H:i:s")))
            ->orderBy('a.created', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
