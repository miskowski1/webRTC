<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Message\Message;
use Web\SocketBundle\Server\Conference\Room;

/**
 * Class GeneralHandler
 * @package Web\SocketBundle\Handler
 */
class GeneralHandler implements HandlerInterface
{
    /**
     * DO NOTHING
     */
    public function __construct(){}

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        $message->setUser($connection->getUser()->getId());
        $room->broadcast($message, $connection);
    }
}