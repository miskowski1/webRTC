<?php

namespace Web\SocketBundle\Handler;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Class Conference
 * @package Web\SocketBundle\Handler
 */
class Conference implements MessageComponentInterface
{
    protected $leader;
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->leader = null;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        $data = json_decode($msg);
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n"
            ,
            $from->resourceId,
            var_export($data, true),
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );
        if ($data->key == 'init') {
            $this->leader = $from;

            return;
        } elseif ($data->key == 'join') {
            $msg = $data;
        } else {
            $msg = $data;
        }

        foreach ($this->clients as $client) {
            if ($from != $client) {
                // The sender is not the receiver, send to each client connected
                $client->send(json_encode($msg));
            }
        }

        if ( !($data->key == 'message' && is_object(
                $data->data
            ) && isset($data->data->type) && $data->data->type == 'candidate')
        ) {
            $from->send(json_encode($msg));
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        if ($this->leader == $conn) {
            $this->leader = $this->clients->current();
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
} 