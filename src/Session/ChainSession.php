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
    }
    public function exec(string $cmdCommand)
    {
        fwrite($this->shell, $cmdCommand);
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
        return $this->chainContext;
    }


    public function getExecContext()
    {
        return $this->chainContext;
    }

}