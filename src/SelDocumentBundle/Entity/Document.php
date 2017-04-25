<?php
namespace SelDocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="document")
 * @ORM\HasLifecycleCallbacks
 */
class Document
{
    protected $tmp;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    public $path;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    public $size;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    public $extension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $subfolder;

    public function getCustomFileName() {
        return $this->size . '_' . $this->path;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        $this->size = $file->getClientSize();
        $this->extension = $file->guessExtension();
        $this->path = $file->getClientOriginalName();
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->getCustomFileName();
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : '/' . $this->getUploadDir().'/'.$this->getCustomFileName();
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        if ($this->subfolder) {
            return 'uploads/' . $this->subfolder;
        }
        return 'uploads';
    }

    public function fileExists() {
        return file_exists($this->getAbsolutePath());
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        if (!file_exists($this->getUploadRootDir())) {
            mkdir($this->getUploadRootDir(), 0775, true);
        }

        if ($this->fileExists()) {
            $this->file = null;
            return;
        }

        $this->file->move(
            $this->getUploadRootDir(), $this->getCustomFileName()
        );

        $this->file = null;
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if(file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }
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
     * Set path
     *
     * @param string $path
     *
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return Document
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get size formatted
     *
     * @return string
     */
    public function getFormattedSize()
    {
        if ($this->size > 1000000) {
            $n = $this->size / 1000000;
            return number_format($n, 2, ',', '.') . " Mb";
        }

        if ($this->size > 1000) {
            $n = $this->size / 1000;
            return number_format($n, 2, ',', '.'). " Kb";
        }

        return $this->size . "b";
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set subfolder
     *
     * @param string $subfolder
     *
     * @return Document
     */
    public function setSubfolder($subfolder)
    {
        $this->subfolder = $subfolder;

        return $this;
    }

    /**
     * Get subfolder
     *
     * @return string
     */
    public function getSubfolder()
    {
        return $this->subfolder;
    }
}