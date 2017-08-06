<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Container
 *
 * @ORM\Table(name="container")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContainerRepository")
 */
class Container
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
     * @ORM\Column(name="capacity", type="float")
     */
    private $capacity;

    /**
     * @var float
     *
     * @ORM\Column(name="used_volume", type="float")
     */
    private $usedVolume;

    /**
     * Many container have One waste_type.
     * @ORM\ManyToOne(targetEntity="WasteType")
     * @ORM\JoinColumn(name="waste_type_id", referencedColumnName="id")
     */
    private $waste_type;

    /**
     * Many containers have One park.
     * @ORM\ManyToOne(targetEntity="Park",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="park_id", referencedColumnName="id")
     */
    private $park;

    /**
     * Container constructor.
     * @param float $capacity
     * @param float $usedVolume
     * @param $waste_type
     * @param $park
     */
    public function __construct($capacity, $usedVolume, $waste_type, $park)
    {
        $this->capacity = $capacity;
        $this->usedVolume = $usedVolume;
        $this->waste_type = $waste_type;
        $this->park = $park;
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
    public function getPark()
    {
        return $this->park;
    }

    /**
     * @param mixed $park
     */
    public function setPark($park)
    {
        $this->park = $park;
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
     * Set capacity
     *
     * @param float $capacity
     *
     * @return Container
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return float
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set usedVolume
     *
     * @param float $usedVolume
     *
     * @return Container
     */
    public function setUsedVolume($usedVolume)
    {
        $this->usedVolume = $usedVolume;

        return $this;
    }

    /**
     * Get usedVolume
     *
     * @return float
     */
    public function getUsedVolume()
    {
        return $this->usedVolume;
    }
}

