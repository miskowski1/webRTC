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
        if ( $room->isLeader($connection) ) {
            $this->handleLeaderMedia($room, $connection, $message);
        } elseif ( $room->isWatcher($connection) ) {
            $this->handleWatcherMedia($room, $connection, $message);
        }
    }

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handleLeaderMedia(Room $room, Connection $connection, Message $message)
    {
        $payload = $message->getPayload();
        $message = new Message('media', $message->getUser());

        if ($payload == 'READY') {
            $message->setPayload('START');
        } else {
            $message->setPayload('LEADER_ERROR');
        }

        $room->broadcast($message, $connection);
    }

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handleWatcherMedia(Room $room, Connection $connection, Message $message)
    {
        $payload = $message->getPayload();
        $message = new Message('media', $message->getUser());

        if ($payload == 'READY') {
            $message->setPayload('ADD');
        } else {
            $message->setPayload('WATCHER_ERROR');
        }

        $room->broadcast($message, $connection);
    }
} 