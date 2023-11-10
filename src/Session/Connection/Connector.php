<?php

namespace Yaroslam\ServerControlPanel\Session\Connection;

class Connector
{
    private ConnectionInterface $connection;

    private string $host;

    private int $port;

    private $connectionTunnel;

    private array $connectProperties;

    public function __construct(ConnectionInterface $connection, array $connectProperties)
    {
        $this->port = $connectProperties['port'];
        $this->host = $connectProperties['host'];
        $this->connectProperties = $connectProperties;
        $this->connection = $connection;
    }

    public function connect()
    {
        $this->connectionTunnel = ssh2_connect($this->host, $this->port);
        $this->connection->connect($this->connectionTunnel, $this->connectProperties['properties']);
    }

    public function getConnectionTunnel()
    {
        return $this->connectionTunnel;
    }

    public function disconnect()
    {
        return 0;
    }
}
