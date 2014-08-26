<?php

namespace Web\SocketBundle\Server\Message;

use Ratchet\ConnectionInterface;
use Web\SocketBundle\Server\Conference\Connection;

/**
 * Class Message
 * @package Web\SocketBundle\Server\Message
 */
class Message
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @param $type
     * @param $payload
     */
    public function __construct($type, $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    /**
     * @param $json
     * @return null|Message
     * @throws \InvalidArgumentException
     */
    public static function fromJSON($json)
    {
        if ($json) {
            $obj = json_decode($json);
            if (!isset($obj->type) || !isset($obj->payload)) {
                throw new \InvalidArgumentException('Incomplete object passed. ' . $json);
            }
            return new self($obj->type, $obj->payload);
        }
        return null;
    }

    /**
     * @param ConnectionInterface $receiver
     * @return ConnectionInterface
     */
    public function sendTo(ConnectionInterface $receiver)
    {
        echo "<<<<<<< ({$receiver->resourceId}) {$this->getType()}\n";
        return $receiver->send(json_encode($this));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
} 