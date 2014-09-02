<?php

namespace Web\SocketBundle\Server\Conference;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Ratchet\ConnectionInterface;
use Web\Bundle\UserBundle\Entity\Room;
use Web\Bundle\UserBundle\Entity\User;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class Connection
 * @package Web\SocketBundle\Server\Conference
 */
class Connection
{
    /**
     * @var integer
     */
    public $resourceId;

    /**
     * @var \Ratchet\ConnectionInterface
     */
    private $connection;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $manager;

    /**
     * @var Room
     */
    private $room;

    /**
     * @var User
     */
    private $user;

    /**
     * @param ConnectionInterface $connection
     * @param EntityManager $manager
     */
    public function __construct(ConnectionInterface $connection, EntityManager $manager)
    {
        $this->connection = $connection;
        $this->resourceId = $connection->resourceId; //Trust me, it's there
        $this->manager = $manager;
        $this->setRoom();
        $this->setUser();
    }

    /**
     * @param Message $message
     * @return ConnectionInterface
     */
    public function send(Message $message)
    {
        return $message->sendTo($this->connection);
    }

    /**
     * @param Message $message
     */
    public function close(Message $message = null)
    {
        if ($message !== null) {
            $message->sendTo($this->connection);
        }
        $this->connection->close();
    }

    /**
     * @param $key
     * @return string|null
     */
    private function getQuery($key)
    {
        return $this->connection->WebSocket->request->getQuery()[$key];
    }

    /**
     *
     */
    public function setRoom()
    {
        $roomID = $this->getQuery('room');
        $this->room = $this->manager->getRepository('WebUserBundle:Room')->findOneBy(array('token' => $roomID));
        if ( $this->room == null ) {
            throw new EntityNotFoundException('Not found');
        }
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     *
     */
    public function setUser()
    {
        $userToken = $this->getQuery('user');
        $this->user = $this->manager->getRepository('WebUserBundle:User')->find($userToken);
        if ( $this->user == null ) {
            throw new EntityNotFoundException('Not found');
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
} 