<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\User;
use AppBundle\Entity\Service;

/**
 * Exchange
 *
 * @ORM\Table(name="exchange")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExchangeRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\BalanceKeeper"})
 * @ORM\HasLifecycleCallbacks()
 */
class Exchange
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $creditUser;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $debitUser;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

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

    public function getEntityName() {
        return 'Exchange';
    }


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
     * Set creditUser
     *
     * @param User $creditUser
     *
     * @return Exchange
     */
    public function setCreditUser(User $creditUser)
    {
        $this->creditUser = $creditUser;

        return $this;
    }

    /**
     * Get creditUser
     *
     * @return User
     */
    public function getCreditUser()
    {
        return $this->creditUser;
    }

    /**
     * Set debitUser
     *
     * @param User $debitUser
     *
     * @return Exchange
     */
    public function setDebitUser(User $debitUser)
    {
        $this->debitUser = $debitUser;

        return $this;
    }

    /**
     * Get debitUser
     *
     * @return User
     */
    public function getDebitUser()
    {
        return $this->debitUser;
    }

    /**
     * Set service
     *
     * @param Service $service
     *
     * @return Exchange
     */
    public function setService(Service $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Exchange
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Exchange
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
}

