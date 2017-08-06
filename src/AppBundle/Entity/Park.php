<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Park
 *
 * @ORM\Table(name="park")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParkRepository")
 */
class Park
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
     * @ORM\Column(name="street_name", type="string", length=255)
     */
    private $streetName;

    /**
     * @var string
     *
     * @ORM\Column(name="house_number", type="string", length=255)
     */
    private $houseNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="post_code", type="string", length=255)
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255)
     */
    private $phone_number;

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param string $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * Many parks have One commune.
     * @ORM\ManyToOne(targetEntity="Commune",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     */
    private $commune;

    /** @ORM\OneToMany(targetEntity="Notification", mappedBy="notification") */
    private $notifications;

    /**
     * Park constructor.
     * @param string $streetName
     * @param string $houseNumber
     * @param string $postCode
     * @param string $city
     * @param $commune
     */
    public function __construct($phone_number, $streetName, $houseNumber, $postCode, $city, $commune)
    {
        $this->phone_number = $phone_number;
        $this->streetName = $streetName;
        $this->houseNumber = $houseNumber;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->commune = $commune;
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @return mixed
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param mixed $commune
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;
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
     * Set streetName
     *
     * @param string $streetName
     *
     * @return Park
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set houseNumber
     *
     * @param string $houseNumber
     *
     * @return Park
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * Get houseNumber
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     *
     * @return Park
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Park
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
}

