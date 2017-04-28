<?php

namespace SelDocumentBundle\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class DocumentManager
 *
 * @package SelDocumentBundle\Entity
 */
class DocumentManager
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var DocumentRepository
     */
    protected $repo;
    /**
     * @var string
     */
    protected $class;

    /**
     * ServiceManager constructor.
     *
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
        $this->repo = $em->getRepository("SelDocumentBundle:Document");
    }

    /**
     * @param string|UploadedFile $file
     * @param string $subfolder
     * @return Document
     * @throws \Exception
     */
    public function createDocument($file, $subfolder)
    {
        if (is_string($file)) {
            $doc = $this->repo->findOneBy(array("path" => $file, "subfolder" => $subfolder));

            if ($doc) {
                return $doc;
            } else {
                throw new \Exception('Can\'t create Document from string');
            }
        }

        if ($file instanceof Document) {
            $doc = $this->repo->findOneBy(array("path" => $file->getPath(), "subfolder" => $subfolder));

            if ($doc) {
                return $doc;
            } else {
                return $file;
            }
        }

        $document = new Document();
        $document->setSubfolder($subfolder);
        $document->setFile($file);

        if ($document->fileExists()) {
            $doc = $this->repo->findOneBy(array("path" => $document->getPath(), "subfolder" => $subfolder));

            if ($doc) {
                return $doc;
            }
        }

        return $document;
    }

    /**
     * @param Document $document
     *
     * @return DocumentManager
     */
    public function saveDocument(Document $document)
    {
        $this->em->persist($document);
        $this->em->flush();

        return $this;
    }
}