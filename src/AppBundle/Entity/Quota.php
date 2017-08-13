<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quota
 *
 * @ORM\Table(name="quota")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuotaRepository")
 */
class Quota
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
     * @ORM\Column(name="volume", type="float", nullable=true)
     */
    private $volume;

    /**
     * Many quotas have One user.
     * @ORM\ManyToOne(targetEntity="User",cascade={"persist", "remove"},inversedBy="quotas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Many quotas have One waste type.
     * @ORM\ManyToOne(targetEntity="WasteType",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="waste_type_id", referencedColumnName="id")
     */
    private $waste_type;

    /**
     * Quota constructor.
     * @param float $volume
     * @param $user
     * @param $waste_type
     */
    public function __construct($volume, $user, $waste_type)
    {
        $this->volume = $volume;
        $this->user = $user;
        $this->waste_type = $waste_type;
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
     * Set volume
     *
     * @param float $volume
     *
     * @return Quota
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume
     *
     * @return float
     */
    public function getVolume()
    {
        return $this->volume;
    }
}

