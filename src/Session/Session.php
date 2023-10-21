<?php
namespace Happy\ServerControlPanel\Session;

use Happy\ServerControlPanel\Session\Connection\ConnectionInterface;
use Happy\ServerControlPanel\Session\Connection\Connector;

class Session
{
    private Connector $connector;
    private bool $ifResult;
    private array $chainContext;
    private array $context;

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
        $this->context = ["output" => stream_get_contents($stream), "error" => stream_get_contents($errorStream)];
        fclose($errorStream);
        fclose($stream);
        return $this;
    }


    public function if(string $cmdCommand, string $ifCondition, string $mustIn="output")
    {
        $execRes = $this->exec($cmdCommand)->getExecContext();
        $this->ifResult = preg_match("/$ifCondition/", $execRes[$mustIn]);
        var_dump("if ok");
        return $this;
    }

    public function then(string $cmdCommand)
    {
        if($this->ifResult)
        {
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }
        var_dump("then ok");
        return $this;
    }

    public function else(string $cmdCommand)
    {
        if(!$this->ifResult){
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }
        var_dump("else ok");
        return $this;
    }

    public function apply()
    {
        $this->NullContext();
        var_dump("apply");
        return $this->chainContext;
    }

    private function NullContext()
    {
        unset($this->ifResult);
        unset($this->context);
    }

    public function getExecContext()
    {
        return $this->context;
    }

    public function __destruct()
    {
        $this->connector->disconnect();
    }
}
