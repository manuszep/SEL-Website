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
 * @ORM\Entity()
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_path;

    /**
     * @Assert\File(maxSize="6000000")
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
    public function setPicture(UploadedFile $file = null)
    {
        $this->picture = $file;

        return $this;
    }

    /**
     * Sets picture path.
     *
     * @param string $path
     *
     * @return Article
     */
    public function setPicturePath($path) {
        $this->picture_path = $path;

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

    /**
     * Get absolute image path
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->picture_path
            ? null
            : $this->getUploadRootDir().'/'.$this->picture_path;
    }

    /**
     * Get image web path
     *
     * @return null|string
     */
    public function getPictureWebPath()
    {
        return null === $this->picture_path
            ? null
            : '/' . $this->getUploadDir().'/'.$this->picture_path;
    }

    /**
     * Get dir path for upload
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * Get relative upload directory
     *
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/articles';
    }

    /**
     * Send file to server
     *
     * @return Article
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getPicture()) {
            return $this;
        }

        $uuid = uniqid() . '.' . $this->getPicture()->guessExtension();

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getPicture()->move(
            $this->getUploadRootDir(),
            $uuid
        );

        // set the path property to the filename where you've saved the file
        $this->picture_path = $uuid;

        // clean up the file property as you won't need it anymore
        $this->picture = null;

        return $this;
    }

    public function addDocument(Document $document)
    {
        $this->documents[] = $document;
        return $this;
    }

    public function setDocuments(ArrayCollection $documents = null) {
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
}