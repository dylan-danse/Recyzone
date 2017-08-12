<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements \JsonSerializable
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
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @param string $postCode
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    }

    /**
     * @return mixed
     */
    public function getQuotas()
    {
        return $this->quotas;
    }

    /**
     * @param mixed $quotas
     */
    public function setQuotas($quotas)
    {
        $this->quotas = $quotas;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="post_code", type="string", length=255, nullable=true)
     * )
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(name="numberOfChild", type="integer", nullable=true)
     * )
     */
    private $numberOfChild;

    /**
     * @var string
     *
     * @ORM\Column(name="numberOfAdult", type="integer", nullable=true)
     * )
     */
    private $numberOfAdult;

    /** @ORM\OneToMany(targetEntity="Quota", mappedBy="household") */
    private $quotas;

    /**
     * @var string
     *
     * @ORM\Column(name="correction_coeff", type="float", nullable=true)
     * )
     */
    private $correctionCoeff;


    /**
     * User constructor.
     * @param string $firstName
     * @param string $lastName
     * @param string $streetName
     * @param string $houseNumber
     * @param string $houseBox
     * @param string $commune
     * @param string $city
     * @param string $numberOfChild
     * @param string $numberOfAdult
     * @param string $correctionCoeff
     * @param $role
     */
    public function __construct($password,$email,$username,$firstName, $lastName, $streetName, $houseNumber, $houseBox, $commune, $city, $postCode, $numberOfChild, $numberOfAdult, $role, $park)
    {
        $this->setPlainPassword($password);
        $this->setEmail($email);
        $this->setEnabled(true);
        $this->setUsername($username);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->streetName = $streetName;
        $this->houseNumber = $houseNumber;
        $this->houseBox = $houseBox;
        $this->commune = $commune;
        $this->city = $city;
        $this->postCode = $postCode;
        $this->numberOfChild = $numberOfChild;
        $this->numberOfAdult = $numberOfAdult;
        $this->setRole($role);
        $this->setPark($park);

        //TODO : without control structure ?
        if($numberOfChild<=3){
            if($numberOfAdult<=2){
                $this->correctionCoeff = 0;
            } elseif ($numberOfAdult<=4){
                $this->correctionCoeff = 5;
            } else {
                $this->correctionCoeff = 10;
            }
        } else {
            if($numberOfAdult<=2){
                $this->correctionCoeff = 5;
            } elseif ($numberOfAdult<=4){
                $this->correctionCoeff = 10;
            } else {
                $this->correctionCoeff = 15;
            }
        }
        $this->quotas = new ArrayCollection();
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
     * @return string
     */
    public function getCorrectionCoeff()
    {
        return $this->correctionCoeff;
    }

    /**
     * @param string $correctionCoeff
     */
    public function setCorrectionCoeff($correctionCoeff)
    {
        $this->correctionCoeff = $correctionCoeff;
    }


    /**
     * Many Users have One Role.
     * @ORM\ManyToOne(targetEntity="Role",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    /**
     * Many Users have One Park.
     * @ORM\ManyToOne(targetEntity="Park",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="park_id", referencedColumnName="id")
     */
    private $park;



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
