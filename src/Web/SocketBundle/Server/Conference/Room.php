<?php

namespace Web\SocketBundle\Server\Conference;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class Room
 * @package Web\SocketBundle\Server\Conference
 */
class Room
{
    /**
     * @var string Hash
     */
    private $id;

    /**
     * @var ConnectionInterface
     */
    private $leader;

    /**
     * @var ArrayCollection
     */
    private $watchers;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @param EntityManager $manager
     * @param string $roomID token
     */
    public function __construct(EntityManager $manager, $roomID)
    {
        $this->manager = $manager;
        $this->id = $roomID;
        // Add room check, if not throw exception
        $this->watchers = new ArrayCollection();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function addConnection(ConnectionInterface $conn)
    {
        //Check is it leader or not and add
        //If not, throw exception
    }

    /**
     * @param ConnectionInterface $leader
     */
    public function setLeader(ConnectionInterface $leader)
    {
        $this->leader = $leader;
    }

    /**
     * @param ConnectionInterface $watcher
     */
    public function addWatcher(ConnectionInterface $watcher)
    {
        $this->watchers[] = $watcher;
    }

    /**
     * @param ConnectionInterface $watcher
     */
    public function dropWatcher(ConnectionInterface $watcher)
    {
        $this->watchers->removeElement($watcher);
        $watcher->send(new Message('Bye', 'Removed from room'));
        $watcher->close();
    }
} 