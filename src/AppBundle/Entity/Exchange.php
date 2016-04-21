<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Exchange
 *
 * @ORM\Table(name="exchange")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExchangeRepository")
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
     * @var int
     *
     * @ORM\Column(name="credit_user", type="integer")
     */
    private $creditUser;

    /**
     * @var int
     *
     * @ORM\Column(name="debit_user", type="integer")
     */
    private $debitUser;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="cu_balance", type="float")
     */
    private $cuBalance;

    /**
     * @var float
     *
     * @ORM\Column(name="du_balance", type="float")
     */
    private $duBalance;

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creditUser
     *
     * @param integer $creditUser
     *
     * @return Exchange
     */
    public function setCreditUser($creditUser)
    {
        $this->creditUser = $creditUser;

        return $this;
    }

    /**
     * Get creditUser
     *
     * @return int
     */
    public function getCreditUser()
    {
        return $this->creditUser;
    }

    /**
     * Set debitUser
     *
     * @param integer $debitUser
     *
     * @return Exchange
     */
    public function setDebitUser($debitUser)
    {
        $this->debitUser = $debitUser;

        return $this;
    }

    /**
     * Get debitUser
     *
     * @return int
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
     * Set cuBalance
     *
     * @param float $cuBalance
     *
     * @return Exchange
     */
    public function setCuBalance($cuBalance)
    {
        $this->cuBalance = $cuBalance;

        return $this;
    }

    /**
     * Get cuBalance
     *
     * @return float
     */
    public function getCuBalance()
    {
        return $this->cuBalance;
    }

    /**
     * Set duBalance
     *
     * @param float $duBalance
     *
     * @return Exchange
     */
    public function setDuBalance($duBalance)
    {
        $this->duBalance = $duBalance;

        return $this;
    }

    /**
     * Get duBalance
     *
     * @return float
     */
    public function getDuBalance()
    {
        return $this->duBalance;
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

    // TODO: Add create, delete and update listeners to apply changes on the concerned users
    // TODO: Check if $cu_balance and $du_balance are necessary because if an exchange is deleted, every older exchanges must be updated.
}

