<?php

namespace Web\SocketBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Web\SocketBundle\Handler\Conference;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServerCommand
 * @package Web\SocketBundle\Command
 */
class ServerCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('socket:run')
            ->setDescription('RUN FOREST');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Conference()
                )
            ),
            8080
        );

        $server->run();
    }
} 