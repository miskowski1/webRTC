<?php

namespace Web\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 */
class Room
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
    private $token;

    /**
     * @var boolean
     */
    private $active_chat;

    /**
     * @var boolean
     */
    private $active_files;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $files;

    /**
     * @var \Web\Bundle\UserBundle\Entity\User
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->token = sha1(time());
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     * @param string $name
     * @return Room
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set token
     * @param string $token
     * @return Room
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
     * Set active_chat
     * @param boolean $activeChat
     * @return Room
     */
    public function setActiveChat($activeChat)
    {
        $this->active_chat = $activeChat;

        return $this;
    }

    /**
     * Get active_chat
     * @return boolean
     */
    public function getActiveChat()
    {
        return $this->active_chat;
    }

    /**
     * Set active_files
     * @param boolean $activeFiles
     * @return Room
     */
    public function setActiveFiles($activeFiles)
    {
        $this->active_files = $activeFiles;

        return $this;
    }

    /**
     * Get active_files
     * @return boolean
     */
    public function getActiveFiles()
    {
        return $this->active_files;
    }

    /**
     * Add files
     * @param \Web\Bundle\UserBundle\Entity\File $files
     * @return Room
     */
    public function addFile(\Web\Bundle\UserBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     * @param \Web\Bundle\UserBundle\Entity\File $files
     */
    public function removeFile(\Web\Bundle\UserBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set owner
     * @param \Web\Bundle\UserBundle\Entity\User $owner
     * @return Room
     */
    public function setOwner(\Web\Bundle\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     * @return \Web\Bundle\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add users
     * @param \Web\Bundle\UserBundle\Entity\User $users
     * @return Room
     */
    public function addUser(\Web\Bundle\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     * @param \Web\Bundle\UserBundle\Entity\User $users
     */
    public function removeUser(\Web\Bundle\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
