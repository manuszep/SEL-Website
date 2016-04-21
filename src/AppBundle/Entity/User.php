<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="db_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=128, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="street_number", type="string", length=10, nullable=true)
     */
    private $street_number;

    /**
     * @var string
     *
     * @ORM\Column(name="street_box", type="string", length=5, nullable=true)
     */
    private $street_box;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=128, nullable=true)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="zip", type="integer", nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="decimal", precision=6, scale=2)
     */
    private $balance = 0;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $picture;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return User
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return int
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set street_number
     *
     * @param string $street_number
     *
     * @return User
     */
    public function setStreetNumber($street_number)
    {
        $this->street_number = $street_number;

        return $this;
    }

    /**
     * Get street_number
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->street_number;
    }

    /**
     * Set street_box
     *
     * @param string $street_box
     *
     * @return User
     */
    public function setStreetBox($street_box)
    {
        $this->street_box = $street_box;

        return $this;
    }

    /**
     * Get street_box
     *
     * @return string
     */
    public function getStreetBox()
    {
        return $this->street_box;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return User
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return User
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set balance
     *
     * @param string $balance
     *
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
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
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        $block1 = trim(implode(" ", array($this->getStreet(), $this->getStreetNumber())));
        if ($block1 != "" && $this->getStreetBox()) {
            $block2 = trim(implode(" - ", array($block1, $this->getStreetBox())));
        } else {
            $block2 = $block1;
        }
        $block3 = trim(implode(" ", array($this->getZip(), $this->getCity())));
        
        $splitter = ",\n";

        $block = array();

        if ($block2 != "") {$block[] = $block2;}
        if ($block3 != "") {$block[] = $block3;}

        return implode($splitter, $block);
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
    public function getWebPath()
    {
        return null === $this->picture_path
            ? null
            : $this->getUploadDir().'/'.$this->picture_path;
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
        return 'uploads/users';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getPicture()) {
            return;
        }

        $uuid = uniqid();

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
}
