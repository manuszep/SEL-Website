<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\QueryBuilder;

class ServiceManager
{
    protected $em;
    protected $repo;
    protected $class;
    protected $filter;
    protected $authorizationChecker;
    protected $tokenStorage;

    public function __construct(EntityManager $em, $class, FilterBuilderUpdater $filter, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository($class);
        $this->filter = $filter;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
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

    public function getQueryBuilder($reverse_order = true, $key = 's') {
        $order = ($reverse_order) ? 'DESC' : 'ASC';
        $qb = $this->repo->createQueryBuilder($key);

        $now = new \DateTime();

        return $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->gt($key . '.expires_at', ':now'),
                    $qb->expr()->isNull($key . '.expires_at')
                )
            )
            ->orderBy($key . '.updated', $order)
            ->setParameter('now', $now->format("Y-m-d H:i:s"));

    }

    public function findAll($reverse_order = true) {
        /** @var QueryBuilder $qb */
        $qb = $this->getQueryBuilder($reverse_order);

        return $qb->getQuery()->getResult();
    }
    
    public function findByUser(User $user, $reverse_order = true) {
        /** @var QueryBuilder $qb */
        $qb = $this->getQueryBuilder($reverse_order);

        $qb->andWhere(
            $qb->expr()->eq('s.user', ':user')
        )->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
    
    public function getFilteredQueryBuilder($form, $reverse_order = true) {
        return $this->filter->addFilterConditions($form, $this->getQueryBuilder($reverse_order));
    }
    
    public function getFilteredQuery($form) {
        return $this->getFilteredQueryBuilder($form)->getQuery();
    }
    
    public function findFiltered($form) {
        return $this->getFilteredQuery($form)->getResult();
    }

    public function saveService(Service $service)
    {
        if (!$this->authorizationChecker->isGranted('ROLE_EDITOR') && !$service->getUser()) {
            $service->setUser($this->tokenStorage->getToken()->getUser());
        }

        $service->upload();
        
        $this->em->persist($service);
        $this->em->flush();
    }
    
    public function deleteService(Service $service) {
        $this->em->remove($service);
        $this->em->flush();
    }
}