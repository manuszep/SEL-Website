<?php

namespace SelServiceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SelServiceBundle\Entity\Service;

class ServiceListener implements EventSubscriber
{
    protected $types;
    protected $domains;

    public function __construct($types, $domains)
    {
        $this->types = $types;
        $this->domains = $domains;
    }

    public function getSubscribedEvents()
    {
        return array('postLoad');
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        /** @var Service $entity */
        $entity = $args->getEntity();

        if ($entity instanceof Service) {
            $entity->setTypesList($this->types);
            $entity->setDomainsList($this->domains);
        }
    }
}