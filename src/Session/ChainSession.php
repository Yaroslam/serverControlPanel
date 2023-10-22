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
        stream_set_blocking($this->shell, true);
        $output_stream = '';
        while($output_streams = fgets($this->shell)){
            $output_stream .= $output_streams;
        }
        $output_array = $output_stream;




        $this->chainContext = ["output" => $output_array, "error" => stream_get_contents($errorStream)];
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