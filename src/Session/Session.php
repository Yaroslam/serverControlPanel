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
        $stream = ssh2_exec($this->connector->getConnectionTunnel(), $cmdCommand);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);
        echo "Output: " . stream_get_contents($stream);
        echo "Error: " . stream_get_contents($errorStream);
        fclose($errorStream);
        fclose($stream);
    }

    public function __destruct()
    {
        $this->connector->disconnect();
    }
}
