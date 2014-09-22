<?php

namespace Web\SocketBundle\Handler;

use Web\SocketBundle\Server\Conference\Connection;
use Web\SocketBundle\Server\Conference\Room;
use Web\SocketBundle\Server\Message\Message;

/**
 * Class ChatHandler
 * @package Web\SocketBundle\Handler
 */
class ChatHandler implements HandlerInterface
{
    public function __construct() {}

    /**
     * @inheritdoc
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        $message->setPayload(
            array(
                'user' => $connection->getUser()->getUsername(),
                'message' => $message->getPayload()
            )
        );
        $room->broadcast($message);
    }
} 