<?php

namespace Web\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RoomInvite
 */
class RoomInvite
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Web\Bundle\UserBundle\Entity\User
     */
    private $user;

    public function __construct()
    {
        $this->token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
     * Set token
     * @param string $token
     * @return RoomInvite
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set user
     * @param \Web\Bundle\UserBundle\Entity\User $user
     * @return RoomInvite
     */
    public function setUser(\Web\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     * @return \Web\Bundle\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
