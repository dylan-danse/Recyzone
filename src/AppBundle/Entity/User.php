<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     * )
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * )
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="streetName", type="string", length=255, nullable=true)
     * )
     */
    private $streetName;

    /**
     * @var string
     *
     * @ORM\Column(name="houseNumber", type="string", length=255, nullable=true)
     * )
     */
    private $houseNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="houseBox", type="string", length=255, nullable=true)
     * )
     */
    private $houseBox;

    /**
     * @var string
     *
     * @ORM\Column(name="commune", type="string", length=255, nullable=true)
     * )
     */
    private $commune;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * )
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="numberOfChild", type="string", length=255, nullable=true)
     * )
     */
    private $numberOfChild;

    /**
     * @var string
     *
     * @ORM\Column(name="numberOfAdult", type="string", length=255, nullable=true)
     * )
     */
    private $numberOfAdult;

    /**
     * Many Users have One Role.
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set streetName
     *
     * @param string $streetName
     *
     * @return User
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
     * @return User
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
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param string $commune
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getHouseBox()
    {
        return $this->houseBox;
    }

    /**
     * @param string $houseBox
     */
    public function setHouseBox($houseBox)
    {
        $this->houseBox = $houseBox;
    }

    /**
     * @return string
     */
    public function getNumberOfChild()
    {
        return $this->numberOfChild;
    }

    /**
     * @param string $numberOfChild
     */
    public function setNumberOfChild($numberOfChild)
    {
        $this->numberOfChild = $numberOfChild;
    }

    /**
     * @return string
     */
    public function getNumberOfAdult()
    {
        return $this->numberOfAdult;
    }

    /**
     * @param string $numberOfAdult
     */
    public function setNumberOfAdult($numberOfAdult)
    {
        $this->numberOfAdult = $numberOfAdult;
    }

    /**
     * Set role
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
