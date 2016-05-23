<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ServiceManager
 *
 * @package AppBundle\Entity
 */
class ServiceManager
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var EntityRepository
     */
    protected $repo;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var FilterBuilderUpdater
     */
    protected $filter;
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
    /**
     * @var array
     */
    protected $serviceFlashTypes;

    /**
     * ServiceManager constructor.
     *
     * @param EntityManager $em
     * @param string $class
     * @param FilterBuilderUpdater $filter
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage $tokenStorage
     * @param array $serviceFlashTypes
     */
    public function __construct(EntityManager $em, $class, FilterBuilderUpdater $filter, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, $serviceFlashTypes)
    {
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $this->filter = $filter;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->serviceFlashTypes = $serviceFlashTypes;
    }

    /**
     * @return Service
     */
    public function createService()
    {
        $class = $this->class;
        $service = new $class();

        return $service;
    }

    /**
     * @param bool $reverse_order
     * @param string $flash_or_normal (all|flash|normal)
     * @param string $key
     * 
     * @return QueryBuilder
     */
    public function getQueryBuilder($reverse_order = true, $flash_or_normal = 'all', $key = 's') {
        $order = ($reverse_order) ? 'DESC' : 'ASC';
        $qb = $this->repo->createQueryBuilder($key);
        $now = new \DateTime();

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->gt($key . '.expires_at', ':now'),
                $qb->expr()->isNull($key . '.expires_at')
            )
        )
        ->orderBy($key . '.updated', $order)
        ->setParameter('now', $now->format("Y-m-d H:i:s"));

        if ($flash_or_normal == 'flash' || $flash_or_normal == 'normal') {
            $exclusion_string = ($flash_or_normal == 'flash') ? '' : 'not ';

            $qb->andWhere($key . '.type ' . $exclusion_string . 'in (:types)')
                ->setParameter('types', $this->serviceFlashTypes);
        }

        return $qb;
    }

    /**
     * @param bool $reverse_order
     * @param string $flash_or_normal (all|flash|normal)
     * 
     * @return array
     */
    public function findAll($reverse_order = true, $flash_or_normal = 'all') {
        /** @var QueryBuilder $qb */
        $qb = $this->getQueryBuilder($reverse_order, $flash_or_normal);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @param bool $reverse_order
     * @param string $flash_or_normal (all|flash|normal)
     *
     * @return array
     */
    public function findByUser(User $user, $reverse_order = true, $flash_or_normal = 'all') {
        /** @var QueryBuilder $qb */
        $qb = $this->getQueryBuilder($reverse_order, $flash_or_normal);

        $qb->andWhere(
            $qb->expr()->eq('s.user', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $form
     * @param bool $reverse_order
     * @param string $flash_or_normal (all|flash|normal)
     *
     * @return object
     */
    public function getFilteredQueryBuilder($form, $reverse_order = true, $flash_or_normal = 'all') {
        $qb = $this->getQueryBuilder($reverse_order, $flash_or_normal);

        return $this->filter->addFilterConditions($form, $qb);
    }

    /**
     * @param $form
     * @param string $flash_or_normal (all|flash|normal)
     *
     * @return mixed
     */
    public function getFilteredQuery($form, $flash_or_normal = 'all') {
        return $this->getFilteredQueryBuilder($form, true, $flash_or_normal)->getQuery();
    }

    /**
     * @param $form
     * @param string $flash_or_normal (all|flash|normal)
     *
     * @return mixed
     */
    public function findFiltered($form, $flash_or_normal = 'all') {
        return $this->getFilteredQuery($form, $flash_or_normal)->getResult();
    }

    /**
     * @param Service $service
     *
     * @return ServiceManager
     */
    public function saveService(Service $service)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_EDITOR') && !$service->getUser()) {
            $service->setUser($this->tokenStorage->getToken()->getUser());
        }

        $service->upload();
        
        $this->em->persist($service);
        $this->em->flush();
        
        return $this;
    }

    /**
     * @param Service $service
     * 
     * @return ServiceManager
     */
    public function deleteService(Service $service) {
        $this->em->remove($service);
        $this->em->flush();
        
        return $this;
    }
}