<?php

namespace Happy\ServerControlPanel\Session;

class ChainSession extends AbstractSession
{


    private bool $ifResult;
    private array $chainContext;
    private $shell;

    public function initChain()
    {
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());
        return $this;
    }
    public function exec(string $cmdCommand)
    {
        fwrite($this->shell, $cmdCommand . PHP_EOL);
        $errorStream = ssh2_fetch_stream($this->shell, SSH2_STREAM_STDERR);
        stream_set_blocking($errorStream, true);


        sleep(1);
        $this->chainContext = ["output" => fgets($this->shell), "error" => stream_get_contents($errorStream)];
        var_dump($this->chainContext["output"]);
        var_dump($this->chainContext["error"], "err");
        return $this;
    }

    public function if(string $cmdCommand, string $ifCondition, string $mustIn="output")
    {
        $execRes = $this->exec($cmdCommand)->getExecContext();
        $this->ifResult = preg_match("/$ifCondition/", $execRes[$mustIn]);
        return $this;
    }

    public function then(string $cmdCommand)
    {
        if($this->ifResult)
        {
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }
        return $this;
    }

    public function else(string $cmdCommand)
    {
        if(!$this->ifResult){
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }
        return $this;
    }


    public function apply()
    {
        return $this->chainContext;
    }


    public function getExecContext()
    {
        return $this->chainContext;
    }

}