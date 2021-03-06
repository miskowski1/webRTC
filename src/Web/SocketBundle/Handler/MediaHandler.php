<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class MediaHandler
 * @package Web\SocketBundle\Handler
 */
class MediaHandler implements HandlerInterface
{

    public function __construct(){}

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        if ($room->isInitiated()) {
            $room->initiateConnection($connection);
        }
    }
} 