<?php

namespace Happy\ServerControlPanel\Session;

// TODO:
//  1)сделать иннер и реал действия
//  2)вывод ошибок
//  3)глобальный и локальный контекст выполнения
//  4)очистка контекста от введенных команд

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
        fwrite($this->shell, $cmdCommand.PHP_EOL);
        sleep(1);
        $outLine = '';
        while ($out = fgets($this->shell)) {
            $outLine .= $out."\n";
        }
        $this->chainContext = ['output' => $outLine];
        var_dump($this->chainContext['output']);

        return $this;
    }

    public function if(string $cmdCommand, string $ifCondition, string $mustIn = 'output')
    {
        $execRes = $this->exec($cmdCommand)->getExecContext();
        $this->ifResult = preg_match("/$ifCondition/", $execRes[$mustIn]);

        return $this;
    }

    public function then(string $cmdCommand)
    {
        if ($this->ifResult) {
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }

        return $this;
    }

    public function else(string $cmdCommand)
    {
        if (! $this->ifResult) {
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
