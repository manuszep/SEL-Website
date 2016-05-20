<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\User;
use AppBundle\Entity\Category;

/**
 * @ORM\Entity()
 * @ORM\Table(name="service")
 * @ORM\HasLifecycleCallbacks()
 */
class Service
{/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
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
     * @ORM\Column(type="integer")
     */
    private $type; // Offre | Demande | Offre Flash | Demande Flash

	/**
     * @ORM\Column(type="integer")
     */
    private $domain; // Service/savoir || Preterie / donnerie

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="services")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $promote;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $picture;

    /**
     * @var datetime $created
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

    private $types_list;
    private $domains_list;

    public function setTypesList($types)
    {
        $this->types_list = $types;

        return $this;
    }

    public function setDomainsList($domains)
    {
        $this->domains_list = $domains;

        return $this;
    }

    public function getTypesList()
    {
        return $this->types_list;
    }

    public function getDomainsList()
    {
        return $this->domains_list;
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
     * @param User $uid
     *
     * @return Service
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
     * @return Service
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
     * @return Service
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
     * Set type
     *
     * @param integer $type
     *
     * @return Service
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeLabel() {
        return $this->getTypesList()[$this->getType()];
    }

    /**
     * Set domain
     *
     * @param integer $domain
     *
     * @return Service
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return integer
     */
    public function getDomain()
    {
        return $this->domain;
    }

    public function getDomainLabel() {
        return $this->getDomainsList()[$this->getDomain()];
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Service
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set promote
     *
     * @param boolean $promote
     *
     * @return Service
     */
    public function setPromote($promote)
    {
        $this->promote = $promote;

        return $this;
    }

    /**
     * Get promote
     *
     * @return boolean
     */
    public function getPromote()
    {
        return $this->promote;
    }

    /**
     * Sets picture.
     *
     * @param UploadedFile $file
     *
     * @return Service
     */
    public function setPicture(UploadedFile $file = null)
    {
        $this->picture = $file;
    }

    public function setPicturePath($path) {
        $this->picture_path = $path;
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
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return Service
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expires_at = $expiresAt;

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
     * @return boolean
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->picture_path
            ? null
            : $this->getUploadRootDir().'/'.$this->picture_path;
    }

    /**
     * @return null|string
     */
    public function getPictureWebPath()
    {
        return null === $this->picture_path
            ? null
            : '/' . $this->getUploadDir().'/'.$this->picture_path;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/services';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getPicture()) {
            return;
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
    }

    public function getSelectLabel() {
        return $this->getUser()->getUsername() . ' - ' . $this->getTitle();
    }

    public function isExpired() {
        return ($this->getExpiresAt()->diff(new \DateTime())->invert == 0);
    }
}
