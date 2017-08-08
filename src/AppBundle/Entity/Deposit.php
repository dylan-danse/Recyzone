<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit
 *
 * @ORM\Table(name="deposit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DepositRepository")
 */
class Deposit
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
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_in_euros", type="float")
     */
    private $amountInEuros;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * Many deposit have One waste type.
     * @ORM\ManyToOne(targetEntity="WasteType")
     * @ORM\JoinColumn(name="waste_type_id", referencedColumnName="id")
     */
    private $waste_type;

    /**
     * Many deposit have One household.
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="household_id", referencedColumnName="id")
     */
    private $household;

    /**
     * Many deposit have One container.
     * @ORM\ManyToOne(targetEntity="Container")
     * @ORM\JoinColumn(name="container_id", referencedColumnName="id")
     */
    private $container;

    /**
     * Deposit constructor.
     * @param float $quantity
     * @param float $amountInEuros
     * @param \DateTime $creationDate
     * @param $waste_type
     * @param $household
     * @param $container
     */
    public function __construct($quantity, $amountInEuros, \DateTime $creationDate, $waste_type, $household, $container)
    {
        $this->quantity = $quantity;
        $this->amountInEuros = $amountInEuros;
        $this->creationDate = $creationDate;
        $this->waste_type = $waste_type;
        $this->household = $household;
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getWasteType()
    {
        return $this->waste_type;
    }

    /**
     * @param mixed $waste_type
     */
    public function setWasteType($waste_type)
    {
        $this->waste_type = $waste_type;
    }

    /**
     * @return mixed
     */
    public function getHousehold()
    {
        return $this->household;
    }

    /**
     * @param mixed $household
     */
    public function setHousehold($household)
    {
        $this->household = $household;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
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
     * Set quantity
     *
     * @param float $quantity
     *
     * @return Deposit
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set amountInEuros
     *
     * @param float $amountInEuros
     *
     * @return Deposit
     */
    public function setAmountInEuros($amountInEuros)
    {
        $this->amountInEuros = $amountInEuros;

        return $this;
    }

    /**
     * Get amountInEuros
     *
     * @return float
     */
    public function getAmountInEuros()
    {
        return $this->amountInEuros;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Deposit
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}

