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
     * Many BillDetails have One Bill.
     * @ORM\ManyToOne(targetEntity="BillDetails",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="bill_details_id", referencedColumnName="id")
     */
    private $billDetails;

    /**
     * @return mixed
     */
    public function getBillDetails()
    {
        return $this->billDetails;
    }

    /**
     * @param mixed $billDetails
     */
    public function setBillDetails($billDetails)
    {
        $this->billDetails = $billDetails;
    }

    /**
     * Deposit constructor.
     * @param float $quantity
     * @param \DateTime $creationDate
     * @param $waste_type
     * @param $household
     * @param $container
     */
    public function __construct($quantity, \DateTime $creationDate, $waste_type, $household, $container, $billDetails)
    {
        $this->quantity = $quantity;
        $this->creationDate = $creationDate;
        $this->waste_type = $waste_type;
        $this->household = $household;
        $this->container = $container;
        $this->billDetails = $billDetails;
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

