<?php

namespace Web\SocketBundle\Server\Conference;

use Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
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

    /**@TODO
     * @var
     */
    private $room;

    /**@TODO
     * @var
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
        throw new NotImplementedException('Nope');
        //@TODO handle room extraction
        //Throw exception if not found
        //$room = $this->manager->getRepository();
    }

    /**
     * @return @TODO
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
        throw new NotImplementedException('Nope');
        //@TODO handle user extraction
        //Throw exception if not found
        //$user = $this->manager->getRepository();
        //
    }

    /**
     * @return @TODO
     */
    public function getUser()
    {
        return $this->user;
    }
} 