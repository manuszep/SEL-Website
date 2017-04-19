<?php

namespace SelExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Exchange
 *
 * @ORM\Table(name="exchange")
 * @ORM\Entity(repositoryClass="SelExchangeBundle\Repository\ExchangeRepository")
 * @ORM\EntityListeners({"AppBundle\EventListener\BalanceKeeperListener"})
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
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $creditUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User")
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
     * @Assert\Range(
     *      min = 0.25
     * )
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

    /**
     * @var bool $hide
     * @ORM\Column(name="hide", type="boolean", nullable=true)
     */
    private $hide;

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
     * @return Exchange
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

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden) {
        $this->hide = $hidden;
    }

    /**
     * @return bool
     */
    public function getHidden() {
        return $this->hide;
    }
}

