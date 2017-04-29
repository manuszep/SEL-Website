<?php

namespace SelDocumentBundle\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use SelDocumentBundle\Entity\DocumentManager;
use SelDocumentBundle\Entity\Document;

class DocumentsCollectionTransformer implements DataTransformerInterface
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
    public function __construct($em, $subFolder)
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
    public function transform($files)
    {
        return $files;
    }

    /**
     * @param mixed $files
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($files)
    {
        foreach ($files as $key => $file) {
            if (!$file->getFile()) {
                $files->remove($key);

                $doc = $this->em->createDocument($file->getPath(), $this->subFolder);

                if ($doc) {
                    $files->add($doc);
                }

                if (!$file->getPath()) {
                    $files->remove($key);
                }
            }
        }

        return $files;
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