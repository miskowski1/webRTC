<?php

namespace Web\SocketBundle\Server\Conference;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Web\SocketBundle\Server\Message\Message;
use Web\Bundle\UserBundle\Entity\Room as EntityRoom;

/**
 * Class Room
 * @package Web\SocketBundle\Server\Conference
 */
class Room
{
    /**
     * @var \Web\Bundle\UserBundle\Entity\Room
     */
    private $room;

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
     * @param EntityRoom $room
     */
    public function __construct(EntityManager $manager, EntityRoom $room)
    {
        $this->manager = $manager;
        $this->room = $room;
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
        return $this->watchers->containsKey($conn->resourceId);
    }

    /**
     * @param Connection $conn
     * @throws \Exception
     */
    public function addConnection(Connection $conn)
    {
        if ($this->room !== $conn->getRoom()) {
            throw new \Exception('Invalid room');
        }
        if ($this->room->getOwner() == $conn->getUser()) {
            $this->setLeader($conn);
        } elseif ($this->room->getUsers()->contains($conn->getUser())) {
            $this->addWatcher($conn);
        } else {
            throw new \Exception('GET THE FUCK OUT');
        }
        $this->broadcast(
            new Message('new', $conn->getUser()->getId(), array(
                $conn->getUser()->getId() => $conn->getUser()->getEmailCanonical()
            )),
            $conn
        );
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
        $this->watchers[$watcher->getUser()->getId()] = $watcher;
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
            if ($watcher != $exclude) {
                $watcher->send($message);
            }
        }

        if ($this->leader != null && $this->leader != $exclude) {
            $this->leader->send($message);
        }
    }

    /**
     * @param Message $message
     * @param int $userId
     */
    public function sendTo(Message $message, $userId)
    {
        if ($this->watchers->containsKey($userId)) {
            $this->watchers[$userId]->send($message);
        }
        if ($this->leader->getUser()->getId() == $userId) {
            $this->leader->send($message);
        }
    }

    /**
     * Send info about all participants
     * @param Connection $connection
     */
    public function shareList(Connection $connection)
    {
        $all = array();
        foreach ($this->watchers as $id => $watcher) {
            $all[$id] = $watcher->getUser()->getEmailCanonical();
        }

        if ( $this->leader ) {
            $all[$this->leader->getUser()->getId()] = $this->leader->getUser()->getEmailCanonical();
        }

        $message = new Message('room', $connection->getUser()->getId(), $all);

        $connection->send($message);
    }

    /**
     * Send who to connect to
     */
    public function initiateConnection()
    {
        $all = array();
        foreach($this->watchers as $id => $tmp) {
            $all[$id] = $id;
        }
        $all[$this->leader->getUser()->getId()] = $this->leader->getUser()->getId();

        foreach($this->watchers as $id => $watcher) {
            unset($all[$id]);
            $message = new Message('connect', null, array_keys($all));
            $watcher->send($message);
        }
    }
} 