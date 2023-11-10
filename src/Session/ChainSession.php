<?php

namespace Happy\ServerControlPanel\Session;

// TODO:
//  1)сделать иннер и реал действия
//  2)вывод ошибок
//  3)глобальный и локальный контекст выполнения
//  4)очистка контекста от введенных команд

use Happy\ServerControlPanel\Session\Commands\BaseCommand;
use Happy\ServerControlPanel\Session\Commands\ElseCommand;
use Happy\ServerControlPanel\Session\Commands\ExecCommand;
use Happy\ServerControlPanel\Session\Commands\IfCommand;
use Happy\ServerControlPanel\Session\Commands\NoneCommand;
use Happy\ServerControlPanel\Session\Commands\ThenCommand;

class ChainSession extends AbstractSession
{
    private array $chainContext;

    private $shell;

    private array $chainCommands;

    private BaseCommand $lastCommand;

    private BaseCommand $lastOperator;

    private int $deepLevel;

    private array $operatorsGraph;

    public function initChain()
    {
        $this->lastCommand = new NoneCommand();
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());
        $this->deepLevel = 0;
        $this->operatorsGraph = [];

        return $this;
    }

    public function exec(string $cmdCommand)
    {
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = new ExecCommand($cmdCommand);
        } else {
            $this->lastCommand->addToBody(new ExecCommand($cmdCommand));
        }

        return $this;
    }

    public function if(string $cmdCommand, string $ifStatment)
    {
        if ($this->deepLevel == 0) {
            $newIf = new IfCommand($cmdCommand, $ifStatment);
            $this->chainCommands[] = $newIf;
            $this->lastCommand = $newIf;
        } else {
            $newIf = new IfCommand($cmdCommand, $ifStatment);
            $this->lastCommand->addToBody($newIf);
            $this->lastCommand = $newIf;
        }
        $this->deepLevel += 1;
        $this->operatorsGraph[$this->deepLevel] = $this->lastCommand;

        return $this;
    }

    public function endIf()
    {
        $this->lastCommand = new NoneCommand();
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel] != null ? $this->operatorsGraph[$this->deepLevel] : new NoneCommand();
        $this->deepLevel -= 1;

        return $this;

    }

    public function then()
    {
        //        $this->lastOperator = $this->lastCommand;
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel];
        $this->lastCommand = new ThenCommand();
        $this->deepLevel += 1;

        return $this;

    }

    public function endThen()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody(new ThenCommand(), 'then');
        $this->lastCommand = $this->operatorsGraph[$this->deepLevel];

        return $this;

    }

    public function else()
    {
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel];
        $this->lastCommand = new ElseCommand();
        $this->deepLevel += 1;

        return $this;

    }

    public function endElse()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody(new ElseCommand(), 'else');
        $this->lastCommand = $this->operatorsGraph[$this->deepLevel];

        return $this;

    }

    public function apply()
    {
        foreach ($this->chainCommands as $command) {
            $command->execution($this->shell);
        }

        return $this;

    }

    public function getExecContext()
    {
        return $this->chainContext;
    }
}
