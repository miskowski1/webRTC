<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class RoomHandler
 * @package Web\SocketBundle\Handler
 */
class RoomHandler implements HandlerInterface
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
        if ( $message->getPayload() == 'ALL') {
            $room->shareList($connection);
        }
    }
} 