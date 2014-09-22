<?php

namespace Web\SocketBundle\Server;

use Web\SocketBundle\Handler;
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
     * @var Handler\HandlerInterface[]
     */
    private $handlers = array();

    /**
     *
     */
    public function __construct()
    {
        $this->handlers['GENERAL'] = new Handler\GeneralHandler();
        $this->handlers['MEDIA'] = new Handler\MediaHandler();
        $this->handlers['ROOM'] = new Handler\RoomHandler();
        $this->handlers['CONNECT'] = new Handler\ConnectHandler();
        $this->handlers['CHAT'] = new Handler\ChatHandler();
        $this->handlers['OFFER'] = $this->handlers['ANSWER'] = $this->handlers['CANDIDATE'] = new Handler\ProxyHandler();
        $this->handlers['BYE'] = new Handler\ByeHandler();
    }

    /**
     * @param Room $room
     * @param Connection $connection
     * @param Message $message
     */
    public function handle(Room $room, Connection $connection, Message $message)
    {
        $type = strtoupper($message->getType());
        if (isset($this->handlers[$type])) {
            $this->handlers[$type]->handle($room, $connection, $message);
        } else {
            $this->handlers['GENERAL']->handle($room, $connection, $message);
            echo "!!!!!!! Unable to handle request {$message->getType()}. Missing handler? !!!!!!!!!\n";
        }
    }
}