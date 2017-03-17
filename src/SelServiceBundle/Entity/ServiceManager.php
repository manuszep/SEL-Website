<?php

namespace SelServiceBundle\Entity;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\QueryBuilder;
use \AppBundle\Entity\User;

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
     * @var ServiceRepository
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
     * @param ServiceRepository $repository
     */
    public function __construct(EntityManager $em, $class, FilterBuilderUpdater $filter, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, $serviceFlashTypes, ServiceRepository $repository)
    {
        $this->em = $em;
        $this->class = $class;
        $this->repo = $repository;
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
     * @param mixed $limit
     *
     * @return array
     */
    public function findAll($reverse_order = true, $limit = false) {
        return $this->repo->findAll($reverse_order, $limit)->getQuery()->getResult();
    }

    /**
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return QueryBuilder
     */
    public function findAllFlash($reverse_order = true, $limit = false) {
        return $this->repo->findAllFlash($reverse_order, $limit)->getQuery()->getResult();
    }

    /**
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return QueryBuilder
     */
    public function findAllNormal($reverse_order = true, $limit = false) {
        return $this->repo->findAllNormal($reverse_order, $limit)->getQuery()->getResult();
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return array
     */
    public function findAllByUser(User $user, $reverse_order = true, $limit = false) {
        return $this->repo->filterByUser($user, $this->repo->findAll($reverse_order, $limit))->getQuery()->getResult();
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return array
     */
    public function findAllFlashByUser(User $user, $reverse_order = true, $limit = false) {
        return $this->repo->filterByUser($user, $this->repo->findAllFlash($reverse_order, $limit))->getQuery()->getResult();
    }

    /**
     * @param \AppBundle\Entity\User $user
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return array
     */
    public function findAllNormalByUser(User $user, $reverse_order = true, $limit = false) {
        return $this->repo->filterByUser($user, $this->repo->findAllNormal($reverse_order, $limit))->getQuery()->getResult();
    }

    /**
     * @param $form
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return mixed
     */
    public function findFiltered($form, $reverse_order = true, $limit = false) {
        return $this->filter->addFilterConditions($form, $this->repo->findAll($reverse_order, $limit))->getQuery()->getResult();
    }

    /**
     * @param $form
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return mixed
     */
    public function findFilteredFlash($form, $reverse_order = true, $limit = false) {
        return $this->filter->addFilterConditions($form, $this->repo->findAllFlash($reverse_order, $limit))->getQuery()->getResult();
    }

    /**
     * @param $form
     * @param bool $reverse_order
     * @param mixed $limit
     *
     * @return mixed
     */
    public function findFilteredNormal($form, $reverse_order = true, $limit = false) {
        return $this->filter->addFilterConditions($form, $this->repo->findAllNormal($reverse_order, $limit))->getQuery()->getResult();
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