<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Interface HandlerInterface
 * @package Web\SocketBundle\Handler
 */
interface HandlerInterface
{
    /**
     * DO NOTHING
     */
    public function __construct();

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message);
}