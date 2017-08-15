<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * WasteType
 *
 * @ORM\Table(name="waste_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WasteTypeRepository")
 */
class WasteType implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="annual_quota", type="integer", nullable=true)
     */
    private $annual_quota;

    /** @ORM\OneToMany(targetEntity="Deposit", mappedBy="waste_type") */
    private $deposits;

    /**
     * WasteType constructor.
     * @param string $name
     * @param string $annual_quota
     */
    public function __construct($name, $annual_quota)
    {
        $this->name = $name;
        $this->annual_quota = $annual_quota;
        $this->deposits = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getAnnualQuota()
    {
        return $this->annual_quota;
    }

    /**
     * @param string $annual_quota
     */
    public function setAnnualQuota($annual_quota)
    {
        $this->annual_quota = $annual_quota;
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
     * Set name
     *
     * @param string $name
     *
     * @return WasteType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
