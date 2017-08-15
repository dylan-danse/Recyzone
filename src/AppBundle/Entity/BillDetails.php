<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BillDetails
 *
 * @ORM\Table(name="bill_details")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillDetailsRepository")
 */
class BillDetails implements \JsonSerializable
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
     * @var float
     *
     * @ORM\Column(name="forfait", type="float", nullable=true)
     */
    private $forfait;

    /**
     * @var float
     *
     * @ORM\Column(name="variable", type="float", nullable=true)
     */
    private $variable;

    /**
     * Many BillDetails have One Bill.
     * @ORM\ManyToOne(targetEntity="Bill",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="bill_id", referencedColumnName="id")
     */
    private $bill;

    /** @ORM\OneToMany(targetEntity="Deposit", mappedBy="billDetails", fetch="EAGER") */
    private $deposits;

    /**
     * BillDetails constructor.
     * @param float $forfait
     * @param float $variable
     * @param $bill
     * @param $deposits
     */
    public function __construct($forfait, $variable, $bill)
    {
        $this->forfait = $forfait;
        $this->variable = $variable;
        $this->bill = $bill;
    }

    /**
     * @return mixed
     */
    public function getBill()
    {
        return $this->bill;
    }

    /**
     * @return mixed
     */
    public function getDeposits()
    {
        return $this->deposits;
    }

    /**
     * @param mixed $deposits
     */
    public function setDeposits($deposits)
    {
        $this->deposits = $deposits;
    }

    /**
     * @param mixed $bill
     */
    public function setBill($bill)
    {
        $this->bill = $bill;
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->deposits;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications)
    {
        $this->deposits = $notifications;
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
     * Set forfait
     *
     * @param float $forfait
     *
     * @return BillDetails
     */
    public function setForfait($forfait)
    {
        $this->forfait = $forfait;

        return $this;
    }

    /**
     * Get forfait
     *
     * @return float
     */
    public function getForfait()
    {
        return $this->forfait;
    }

    /**
     * Set variable
     *
     * @param float $variable
     *
     * @return BillDetails
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return float
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }
}

