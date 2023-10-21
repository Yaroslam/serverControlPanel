<?php
namespace Happy\ServerControlPanel\Session;

use Happy\ServerControlPanel\Session\Connection\ConnectionInterface;
use Happy\ServerControlPanel\Session\Connection\Connector;

class Session
{
    private Connector $connector;

    public function __construct(ConnectionInterface $connectionType, array $connectProperties)
    {
        $this->connector = new Connector($connectionType, $connectProperties);
        $this->connector->connect();


    }

    public function exec(string $cmdCommand)
    {
        ssh2_exec($this->connector->getConnectionTunnel(), $cmdCommand);
    }

    public function __destruct()
    {
        $this->connector->disconnect();
    }
}
