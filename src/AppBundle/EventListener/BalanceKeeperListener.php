<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\User;
use SelExchangeBundle\Entity\Exchange;
use Doctrine\ORM\Event\PostFlushEventArgs;

class BalanceKeeperListener
{
    /** @ORM\PostPersist */
    public function postPersistHandler(Exchange $exchange, LifecycleEventArgs $event) {
        $entityManager = $event->getEntityManager();

        /* @var User $creditUser */
        $creditUser = $exchange->getCreditUser();

        /* @var User $debitUser */
        $debitUser = $exchange->getDebitUser();

        /* @var float $amount */
        $amount = $exchange->getAmount();

        $creditUser->credit($amount);
        $debitUser->debit($amount);

        $entityManager->persist($creditUser);
        $entityManager->persist($debitUser);

        $entityManager->flush(array(
            $creditUser,
            $debitUser
        ));
    }

    /** @ORM\PreUpdate */
    public function preUpdateHandler(Exchange $exchange, LifecycleEventArgs $event) {
        $entityManager = $event->getEntityManager();

        /* @var User $creditUser */
        $creditUser = $exchange->getCreditUser();

        /* @var User $debitUser */
        $debitUser = $exchange->getDebitUser();

        /* @var float $diff */
        $diff = $exchange->getAmount() - $event->getOldValue('amount');

        $creditUser->credit($diff);

        $debitUser->debit($diff);

        $entityManager->persist($creditUser);
        $entityManager->persist($debitUser);
    }

    /** @ORM\PostUpdate */
    public function postUpdateHandler(Exchange $exchange, LifecycleEventArgs $event)
    {
        $entityManager = $event->getEntityManager();

        $entityManager->flush();
    }

    /** @ORM\PostRemove */
    public function postRemoveHandler(Exchange $exchange, LifecycleEventArgs $event) {
        $entityManager = $event->getEntityManager();

        /* @var User $creditUser */
        $creditUser = $exchange->getCreditUser();

        /* @var User $debitUser */
        $debitUser = $exchange->getDebitUser();

        /* @var float $amount */
        $amount = $exchange->getAmount();

        $creditUser->debit($amount);
        $debitUser->credit($amount);

        $entityManager->persist($creditUser);
        $entityManager->persist($debitUser);

        $entityManager->flush(array(
            $creditUser,
            $debitUser
        ));
    }
}
