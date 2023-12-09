<?php

namespace Yaroslam\SSH2\Session;

use Yaroslam\SSH2\Session\Connection\ConnectionInterface;
use Yaroslam\SSH2\Session\Connection\Connector;

abstract class AbstractSession
{
    protected Connector $connector;

    public function __construct(ConnectionInterface $connectionType, array $connectProperties)
    {
        $this->connector = new Connector($connectionType, $connectProperties);
        $this->connector->connect();
    }

    public function getConnection()
    {

    }

    abstract public function apply();

    abstract public function exec(string $cmdCommand);

    public function __destruct()
    {
        $this->connector->disconnect();
    }
}
