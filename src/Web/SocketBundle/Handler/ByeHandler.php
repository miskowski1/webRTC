<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class ByeHandler
 * @package Web\SocketBundle\Handler
 */
class ByeHandler implements HandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(){}

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        $room->removeWatcher($connection);
        $room->notifyLeave($connection);
    }
} 