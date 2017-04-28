<?php

namespace SelDocumentBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityListener {
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        $doc = $entityManager->getRepository('SelDocumentBundle:Document')->findOneBy(array("path" => $entity->getPath(), "subfolder" => $entity->getSubFolder()));

        $f = '/Users/manuszep/Desktop/log.txt';
        file_put_contents($f, json_encode(array("path" => $entity->getPath(), "subfolder" => $entity->getSubFolder())) . "\n", FILE_APPEND | LOCK_EX);
        if ($doc) {
            file_put_contents($f, "Found!\n\n", FILE_APPEND | LOCK_EX);
            $entity->setFile($doc);
        }
    }
}