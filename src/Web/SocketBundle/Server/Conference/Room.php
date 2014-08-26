<?php

namespace Web\SocketBundle\Server\Conference;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
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
     * @var Connection
     */
    private $leader;

    /**
     * @var Connection[]
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
     * @param Connection $conn
     * @return bool
     */
    public function isLeader(Connection $conn)
    {
        return $this->leader == $conn;
    }

    /**
     * @param Connection $conn
     * @return bool
     */
    public function isWatcher(Connection $conn)
    {
        return $this->watchers->contains($conn);
    }

    /**
     * @param Connection $conn
     */
    public function addConnection(Connection $conn)
    {
        //Check is it leader or not and add
        //If not, throw exception
    }

    /**
     * @param Connection $leader
     */
    public function setLeader(Connection $leader)
    {
        $this->leader = $leader;
    }

    /**
     * @param Connection $watcher
     */
    public function addWatcher(Connection $watcher)
    {
        $this->watchers[] = $watcher;
    }

    /**
     * @param Connection $watcher
     */
    public function dropWatcher(Connection $watcher)
    {
        $this->watchers->removeElement($watcher);
        $watcher->send(new Message('Bye', 'Removed from room'));
        $watcher->close();
    }

    /**
     * @param Message $message
     * @param Connection $exclude
     */
    public function broadcast(Message $message, Connection $exclude = null)
    {
        foreach ($this->watchers as $watcher) {
            if ( $watcher != $exclude ) {
                $watcher->send($message);
            }
        }

        if ( $this->leader != $exclude ) {
            $this->leader->send($message);
        }
    }
} 