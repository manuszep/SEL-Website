<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
    
    public function findAll($reverse_order = true) {
        $order = ($reverse_order) ? 'DESC' : 'ASC';
        return $this->repo->findBy(array(), array('updated' => $order));
    }
    
    public function findByUser(User $user, $reverse_order = true) {
        $order = ($reverse_order) ? 'DESC' : 'ASC';
        return $this->repo->findByUser($user, array('updated' => $order));
    }
    
    public function getQueryBuilder($key = 's', $reverse_order = true) {
        $order = ($reverse_order) ? 'DESC' : 'ASC';
        return $this->repo->createQueryBuilder($key)->orderBy('s.updated', $order);
    }
    
    public function getFilteredQueryBuilder($form) {
        return $this->filter->addFilterConditions($form, $this->getQueryBuilder());
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