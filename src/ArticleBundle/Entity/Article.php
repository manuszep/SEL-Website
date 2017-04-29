<?php
namespace ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\User;
use SelDocumentBundle\Entity\Document;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="ArticleBundle\Entity\ArticleRepository")
 * @ORM\Table(name="article")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var Document $picture
     *
     * @ORM\ManyToOne(targetEntity="SelDocumentBundle\Entity\Document", cascade={"persist"})
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=true)
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity="SelDocumentBundle\Entity\Document", cascade={"persist"})
     * @ORM\JoinTable(name="articles_documents",
     *      joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")}
     *      )
     */
    private $documents;

    /**
     * @var \DateTime $published_at
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published_at;

    /**
     * @var \DateTime $expires_at
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires_at;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime $contentChanged
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"title", "body"})
     */
    private $contentChanged;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    public function getEntityName() {
        return 'Article';
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Article
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Article
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set published
     *
     * @param bool $published
     *
     * @return Article
     */
    public function setPublished($published) {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return bool
     */
    public function getPublished() {
        return $this->published;
    }

    /**
     * Sets picture.
     *
     * @param UploadedFile $file
     *
     * @return Article
     */
    public function setPicture(Document $document = null)
    {
        $this->picture = $document;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return UploadedFile
     */
    public function getPicture()
    {
        return $this->picture;
    }

    public function addDocument(Document $document)
    {
        $this->documents->add($document);
        return $this;
    }

    public function removeDocument(Document $document) {
        $this->documents->removeElement($document);
        return $this;
    }

    public function setDocuments($documents = null) {
        $this->documents = $documents;
    }

    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Set published at
     *
     * @param \DateTime $published_at
     *
     * @return Article
     */
    public function setPublishedAt($published_at)
    {
        $this->published_at = $published_at;

        return $this;
    }

    /**
     * Get published at
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expires_at
     *
     * @return Article
     */
    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get contentChanged
     *
     * @return \DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    public function isPublished() {
        $now = new \DateTime();

        return ($this->published && (!$this->published_at || $this->published_at < $now) && (!$this->expires_at || $this->expires_at > $now));
    }

    public function getWhyNotPublished() {
        $now = new \DateTime();

        if (!$this->published) {
            return "Non publié";
        }

        if ($this->published_at && $this->published_at > $now) {
            return "Pas encore publié";
        }

        if ($this->expires_at && $this->expires_at <= $now) {
            return "Expiré";
        }
        return null;
    }
}