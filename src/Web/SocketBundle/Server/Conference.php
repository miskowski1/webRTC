<?php

namespace Web\SocketBundle\Server;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class Conference
 * @package Web\SocketBundle\Server
 */
class Conference implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     * @var Room[]
     */
    protected $rooms;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     *
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->clients = new \SplObjectStorage;
        $this->rooms = new ArrayCollection();
        $this->dispatcher = new Dispatcher();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        echo ">++++++++ ({$conn->resourceId})\n";
        $conn = $this->remakeConnection($conn);
        if ($conn !== null && $this->addWatcher($conn)) {
            $this->clients->attach($conn);
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (!$from = $this->remakeConnection($from)) {
            return;
        }

        $message = Message::fromJSON($msg);
        $room = $this->rooms[$from->getRoom()->getHash()];

        echo ">>>>>>> ({$from->resourceId}) {$message->getType()}\n";

        $this->dispatcher->handle($room, $from, $message);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo ">------- {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "{$conn->resourceId}An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    /**
     * @param Connection $conn
     * @return bool
     */
    public function addWatcher(Connection $conn)
    {
        try {
            $room = $conn->getRoom();
            if (!$this->rooms->containsKey($room->getHash())) {
                $this->rooms[$room->getHash()] = new Room($this->manager, $room);
            }
            $this->rooms[$room->getHash()]->addWatcher($conn);

            return true;
        } catch (\InvalidArgumentException $e) {
            $conn->close(new Message('Bye', 'I don\'t think we\'re in Kansas anymore, Toto.'));
        } catch (\Exception $e) {
            $conn->close(new Message('Bye', 'Something bad happened, Harry'));
        }

        return false;
    }

    /**
     * @param ConnectionInterface $conn
     * @return null|Connection
     */
    private function remakeConnection(ConnectionInterface $conn)
    {
        try {
            return new Connection($conn, $this->manager);
        } catch (\Exception $e) {
            $message = new Message('Bye', 'Wrong data');
            $message->sendTo($conn);
            $conn->close();
        }

        return null;
    }
} 