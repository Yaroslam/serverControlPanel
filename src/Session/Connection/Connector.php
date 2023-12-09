<?php

namespace Yaroslam\SSH2\Session\Connection;

class Connector
{
    private ConnectionInterface $connection;

    private string $host;

    private int $port;

    private $ssh2Connect;

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
        $this->ssh2Connect = ssh2_connect($this->host, $this->port);
        $this->connection->connect($this->ssh2Connect, $this->connectProperties['properties']);
    }

    public function getSsh2Connect()
    {
        return $this->ssh2Connect;
    }

    public function disconnect()
    {
        return 0;
    }
}
