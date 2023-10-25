<?php

namespace Happy\ServerControlPanel\Session;

// TODO:
//  1)сделать иннер и реал действия
//  2)вывод ошибок
//  3)глобальный и локальный контекст выполнения
//  4)очистка контекста от введенных команд

use Happy\ServerControlPanel\Session\Commands\BaseCommand;
use Happy\ServerControlPanel\Session\Commands\CommandClasses;
use Happy\ServerControlPanel\Session\Commands\ExecCommand;
use Happy\ServerControlPanel\Session\Commands\IfCommand;
use Happy\ServerControlPanel\Session\Commands\NoneCommand;

class ChainSession extends AbstractSession
{
    private bool $ifResult;

    private array $chainContext;

    private $shell;

    private array $chainCommands;

    private BaseCommand $lastCommand;

    public function initChain()
    {
        $this->lastCommand = new NoneCommand();
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());

        return $this;
    }

    private function realExec(string $cmdCommand)
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

    public function exec(string $cmdCommand)
    {
        if ($this->lastCommand->getCommandType() == CommandClasses::None ||
            $this->lastCommand->getCommandType() == CommandClasses::Single) {
            $this->chainCommands[] = new ExecCommand($cmdCommand);
        } elseif ($this->lastCommand->getCommandType() == CommandClasses::Block) {
            $this->lastCommand->fillBody(new ExecCommand($cmdCommand));
        } else {
            var_dump('command chain order error');
        }
    }

    private function realIf(string $cmdCommand, string $ifCondition, string $mustIn = 'output')
    {
        $execRes = $this->exec($cmdCommand)->getExecContext();
        $this->ifResult = preg_match("/$ifCondition/", $execRes[$mustIn]);
        return $this;
    }

    public function if(string $cmdCommand, string $ifStatment, $ifOperator)
    {
        if ($this->lastCommand->getCommandType() != CommandClasses::Operator) {
            $this->chainCommands[] = new IfCommand($cmdCommand, $ifOperator, $ifStatment);
        } else {
            var_dump('command chain order error');
        }
    }

    public function endIf()
    {
        if ($this->lastCommand->getCommandName() == 'If') {
            $this->lastCommand = new NoneCommand();
        } else {
            var_dump('command chain order error');
        }
    }

    private function realThen(string $cmdCommand)
    {
        if ($this->ifResult) {
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }

        return $this;
    }

    public function then(string $cmdCommand)
    {
        if ($this->lastCommand->getCommandName() == 'If') {
            $this->lastCommand
        } else {
            var_dump('command chain order error');
        }
    }

    public function endThen()
    {

    }

    private function realElse(string $cmdCommand)
    {
        if (! $this->ifResult) {
            $this->chainContext = $this->exec($cmdCommand)->getExecContext();
        }

        return $this;
    }

    public function else(string $cmdCommand)
    {

    }

    public function endElse()
    {

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
