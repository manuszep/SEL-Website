<?php
namespace SelServiceBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use \AppBundle\Entity\User;
/**
 * ServiceRepository
 */
class ServiceRepository extends \Doctrine\ORM\EntityRepository
{
    protected $flash_ids;

    /**
     * @param $list
     */
    public function setFlashIds($list) {
        $this->flash_ids = $list;
    }

    /**
     * @param bool $reverse
     * @param string $flash_or_normal (all|flash|normal)
     * @param mixed $limit
     * @param string $key
     *
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder($reverse = true, $flash_or_normal = 'all', $limit = false, $key = 's') {
        $order = ($reverse) ? 'DESC' : 'ASC';
        $qb = $this->createQueryBuilder($key);
        $now = new \DateTime();

        $qb->where(
            $qb->expr()->orX( // Where expires_at > now OR expires_at is null
                $qb->expr()->gt($key . '.expires_at', ':now'),
                $qb->expr()->isNull($key . '.expires_at')
            )
        )
            ->orderBy($key . '.updated', $order) // Sort result based on last updated field
            ->setParameter('now', $now->format("Y-m-d H:i:s"));

        if ($flash_or_normal == 'flash' || $flash_or_normal == 'normal') { // If we don't want to get all the results
            $exclusion_string = ($flash_or_normal == 'flash') ? '' : 'not ';

            // Find where type id is or is not in list of flash_ids
            $qb->andWhere($key . '.type ' . $exclusion_string . 'in (:types)')
                ->setParameter('types', $this->flash_ids);
        }

        if ($limit) {
            if ($limit instanceof \DateTime) { // If limit is a date, we do it on updated field.
                $qb->andWhere(
                    $qb->expr()->gt($key . '.updated', ':limit')
                )
                    ->setParameter('limit', $limit->format("Y-m-d H:i:s"));
            } else if (is_numeric($limit)) { // If limit is a number, we limit the # of results.
                $qb->setMaxResults($limit);
            }
        }

        return $qb; // Return the Query Builder so we can continue fine-tuning the query if necessary.
    }

    /**
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return QueryBuilder
     */
    public function findAll($reverse_order = true, $limit = false) {
        return $this->getBaseQueryBuilder($reverse_order, 'all', $limit);
    }

    /**
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return QueryBuilder
     */
    public function findAllFlash($reverse_order = true, $limit = false) {
        return $this->getBaseQueryBuilder($reverse_order, 'flash', $limit);
    }

    /**
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return QueryBuilder
     */
    public function findAllNormal($reverse_order = true, $limit = false) {
        return $this->getBaseQueryBuilder($reverse_order, 'normal', $limit);
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @param bool|QueryBuilder $qb
     *
     * @return array
     */
    public function filterByUser(User $user, $qb = false) {
        if (!$qb) {
            $qb = $this->findAll();
        }

        $qb->andWhere(
            $qb->expr()->eq('s.user', ':user')
        )->setParameter('user', $user);

        return $qb;
    }
}
