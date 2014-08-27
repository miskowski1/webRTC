<?php
namespace Web\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{
    /**
     * {@inheritDoc}
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $rooms;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $invitations;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->rooms       = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invitations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add rooms
     * @param \Web\Bundle\UserBundle\Entity\Room $rooms
     * @return User
     */
    public function addRoom(\Web\Bundle\UserBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;

        return $this;
    }

    /**
     * Remove rooms
     * @param \Web\Bundle\UserBundle\Entity\Room $rooms
     */
    public function removeRoom(\Web\Bundle\UserBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * Add invitations
     * @param \Web\Bundle\UserBundle\Entity\Room $invitations
     * @return User
     */
    public function addInvitation(\Web\Bundle\UserBundle\Entity\Room $invitations)
    {
        $this->invitations[] = $invitations;

        return $this;
    }

    /**
     * Remove invitations
     * @param \Web\Bundle\UserBundle\Entity\Room $invitations
     */
    public function removeInvitation(\Web\Bundle\UserBundle\Entity\Room $invitations)
    {
        $this->invitations->removeElement($invitations);
    }

    /**
     * Get invitations
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * @var \DateTime
     */
    private $last_activity;


    /**
     * Set last_activity
     * @param \DateTime $lastActivity
     * @return User
     */
    public function setLastActivity($lastActivity)
    {
        $this->last_activity = $lastActivity;

        return $this;
    }

    /**
     * Get last_activity
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->last_activity;
    }

    public function isActiveNow()
    {
        $this->setLastActivity(new \DateTime());

        return $this;
    }

    /**
     * @var \Web\Bundle\UserBundle\Entity\RoomInvite
     */
    private $invite;


    /**
     * Set invite
     * @param \Web\Bundle\UserBundle\Entity\RoomInvite $invite
     * @return User
     */
    public function setInvite(\Web\Bundle\UserBundle\Entity\RoomInvite $invite = null)
    {
        $this->invite = $invite;

        return $this;
    }

    /**
     * Get invite
     * @return \Web\Bundle\UserBundle\Entity\RoomInvite
     */
    public function getInvite()
    {
        return $this->invite;
    }
}
