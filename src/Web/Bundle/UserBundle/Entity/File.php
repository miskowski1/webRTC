<?php

namespace Web\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 */
class File
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $physical;

    /**
     * @var \Web\Bundle\UserBundle\Entity\Room
     */
    private $room;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return File
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
     * Set physical
     *
     * @param string $physical
     * @return File
     */
    public function setPhysical($physical)
    {
        $this->physical = $physical;

        return $this;
    }

    /**
     * Get physical
     *
     * @return string 
     */
    public function getPhysical()
    {
        return $this->physical;
    }

    /**
     * Set room
     *
     * @param \Web\Bundle\UserBundle\Entity\Room $room
     * @return File
     */
    public function setRoom(\Web\Bundle\UserBundle\Entity\Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Web\Bundle\UserBundle\Entity\Room 
     */
    public function getRoom()
    {
        return $this->room;
    }
}
