<?php

namespace Happy\ServerControlPanel\Session;

class ChainSession extends AbstractSession
{


    private bool $ifResult;
    private array $chainContext;
    private $shell;

    public function initChain()
    {
	var_dump("init");
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());
        return $this;
    }
    public function exec(string $cmdCommand)
    {
	var_dump("exec " . $cmdCommand);
        fwrite($this->shell, $cmdCommand . PHP_EOL);
	var_dump("sleep");
        sleep(1);
	$outLine = '';
	while($out = fgets($this->shell))
	{
		$outLine.=$out . "\n";
	}
        $this->chainContext = ["output" => $outLine];
        var_dump($this->chainContext["output"]);
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
