<?php

namespace Web\SocketBundle\Server\Message;

use Ratchet\ConnectionInterface;

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
     * @var string
     */
    private $user;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @param $type
     * @param $user
     * @param string $payload
     */
    public function __construct($type, $user, $payload = '')
    {
        $this->type = $type;
        $this->user = $user;
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
            if (!isset($obj->type) || !isset($obj->user) || !isset($obj->payload)) {
                throw new \InvalidArgumentException('Incomplete object passed. ' . $json);
            }
            return new self($obj->type, $obj->user, $obj->payload);
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
        echo json_encode(get_object_vars($this))."\n";
        return $receiver->send(json_encode(get_object_vars($this)));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return Message
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param $payload
     * @return Message
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
} 