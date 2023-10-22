<?php

namespace Happy\ServerControlPanel\Session;

use Happy\ServerControlPanel\Session\Connection\ConnectionInterface;
use Happy\ServerControlPanel\Session\Connection\Connector;

abstract class AbstractSession
{
    protected Connector $connector;
    public function __construct(ConnectionInterface $connectionType, array $connectProperties)
    {
        $this->connector = new Connector($connectionType, $connectProperties);
        $this->connector->connect();
    }

    abstract public function apply();
    abstract public function exec(string $cmdCommand);

    public function __destruct()
    {
        $this->connector->disconnect();
    }

}