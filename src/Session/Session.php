<?php
namespace Happy\ServerControlPanel\Session;

use Happy\ServerControlPanel\Session\Connection\ConnectionInterface;
use Happy\ServerControlPanel\Session\Connection\Connector;

class Session extends AbstractSession
{
    private array $context;

    public function exec(string $cmdCommand)
    {
        $stream = ssh2_exec($this->connector->getConnectionTunnel(), $cmdCommand);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);
        stream_set_blocking($stream, true);
        $this->context = ["output" => stream_get_contents($stream), "error" => stream_get_contents($errorStream)];
        var_dump($this->context["output"]);
        fclose($errorStream);
        fclose($stream);
        return $this;
    }


    public function apply()
    {
        return $this->context;
    }


    public function getExecContext()
    {
        return $this->context;
    }


}
