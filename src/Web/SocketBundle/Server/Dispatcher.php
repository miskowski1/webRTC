<?php

namespace Web\SocketBundle\Server;

use Web\SocketBundle\Handler\HandlerInterface;
use Web\SocketBundle\Handler\MediaHandler;
use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class Dispatcher
 * @package Web\SocketBundle\Server
 */
class Dispatcher
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers = array();

    /**
     *
     */
    public function __construct()
    {
        $this->handlers['media'] = new MediaHandler();
    }

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        if (isset($this->handlers[$message->getType()])) {
            $this->handlers[$message->getType()]->handle($room, $connection, $message);
        } else {
            echo "!!!!!!! Unable to handle request {$message->getType()}. Missing handler? !!!!!!!!!\n";
        }
    }
}