<?php

namespace SelDocumentBundle\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use SelDocumentBundle\Entity\DocumentManager;
use SelDocumentBundle\Entity\Document;

class DocumentTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;
    private $entityClass;
    private $entityType;
    private $entityRepository;
    private $subFolder;

    /**
     * @param ObjectManager $om
     */
    public function __construct(DocumentManager $em, $subFolder)
    {
        $this->em = $em;
        $this->setEntityClass("SelDocumentBundle\\Entity\\Document");
        $this->setEntityRepository("SelDocumentBundle:Document");
        $this->setEntityType("document");
        $this->subFolder = $subFolder;
    }

    /**
     * @param mixed $entity
     *
     * @return integer
     */
    public function transform($entity)
    {
        return $entity;
    }

    /**
     * @param mixed $files
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($file)
    {
        if (!$file || !count($file)) {
            return null;
        }

        if (is_array($file)) {
            $doc = $this->em->createDocument($file[0], $this->subFolder);
            return $doc;
        } else {
            $doc = $this->em->createDocument($file, $this->subFolder);
            return $doc;
        }
    }

    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function setEntityRepository($entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

}