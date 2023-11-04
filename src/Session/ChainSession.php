<?php

namespace Happy\ServerControlPanel\Session;

// TODO:
//  1)сделать иннер и реал действия
//  2)вывод ошибок
//  3)глобальный и локальный контекст выполнения
//  4)очистка контекста от введенных команд

use Happy\ServerControlPanel\Session\Commands\BaseCommand;
use Happy\ServerControlPanel\Session\Commands\CommandClasses;
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

    public function initChain()
    {
        $this->lastCommand = new NoneCommand();
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());

        return $this;
    }

    public function exec(string $cmdCommand)
    {
        if ($this->lastCommand->getCommandType() == CommandClasses::None ||
            $this->lastCommand->getCommandType() == CommandClasses::Single) {
            $this->chainCommands[] = new ExecCommand($cmdCommand);
        } elseif ($this->lastCommand->getCommandType() == CommandClasses::Block) {
            $this->lastCommand->addToBody(new ExecCommand($cmdCommand));
        } else {
            var_dump('command chain order error');
        }

        return $this;
    }

    public function if(string $cmdCommand, string $ifStatment)
    {
        $this->chainCommands[] = new IfCommand($cmdCommand, $ifStatment);

        return $this;
    }

    public function endIf()
    {
        $this->lastCommand = new NoneCommand();

        return $this;

    }

    public function then()
    {
        $this->lastOperator = $this->lastCommand;
        $this->lastCommand = new ThenCommand();

        return $this;

    }

    public function endThen()
    {
        $this->lastOperator->addToBody($this->lastCommand, 'Then');
        $this->lastCommand = $this->lastOperator;

        return $this;

    }

    public function else()
    {
        $this->lastOperator = $this->lastCommand;
        $this->lastCommand = new ElseCommand();

        return $this;

    }

    public function endElse()
    {
        $this->lastOperator->addToBody(new ElseCommand(), 'else');
        $this->lastCommand = $this->lastOperator;

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
