<?php

namespace SelDocumentBundle\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
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
    public function __construct(EntityManager $em, $subFolder)
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

    protected function findOrCreateDocument($file) {
        $repo = $this->em->getRepository($this->entityRepository);
        $document = new Document();
        $document->setSubfolder($this->subFolder);

        if (is_string($file)) {
            $doc = $repo->findOneBy(array("path" => $file, "subfolder" => $this->subFolder));

            if ($doc) {
                return $doc;
            } else {
                $document->setPath($file);

                return $document;
            }
        }

        $document->setFile($file);

        if ($document->fileExists()) {
            $doc = $repo->findOneBy(array("path" => $document->getPath(), "subfolder" => $this->subFolder));

            if ($doc) {
                return $doc;
            }
        }

        return $document;
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
        if (!$files || !count($files)) {
            return null;
        }

        if (is_array($files)) {
            $documents = new ArrayCollection();

            foreach($files as $file) {
                $documents->add($this->findOrCreateDocument($file));
            }
        } else {
            $documents = $this->findOrCreateDocument($files);
        }

        return $documents;
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