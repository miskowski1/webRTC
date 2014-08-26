<?php

namespace Web\SocketBundle\Server;

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
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        //@TODO handle
    }
}