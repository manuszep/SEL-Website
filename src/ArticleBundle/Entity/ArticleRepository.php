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
    public function findPublished($key = 'a', $fetch = true) {
        $qb = $this->createQueryBuilder($key);
        $now = new \DateTime();

        $qb->where($qb->expr()->orX( // Where expires_at > now OR expires_at is null
                $qb->expr()->gt($key . '.expires_at', ':now1'),
                $qb->expr()->isNull($key . '.expires_at')
            ))
            ->andWhere(
                $qb->expr()->orX( // Where published_at < now OR published_at is null
                    $qb->expr()->lt($key . '.published_at', ':now2'),
                    $qb->expr()->isNull($key . '.published_at')
                )
            )
            ->andWhere($key . '.published = 1')
            ->setParameters(array('now1' => $now->format("Y-m-d H:i:s"), 'now2' => $now->format("Y-m-d H:i:s")))
            ->orderBy($key . '.created', 'DESC');

        if ($fetch) {
            return $qb->getQuery()->getResult();
        }

        return $qb;
    }
}
