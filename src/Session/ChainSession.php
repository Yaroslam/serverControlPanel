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

    private array $blockGraph;

    private string $currBlock;

    public function initChain()
    {
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());
        $this->deepLevel = 0;
        $this->operatorsGraph = [];
        $this->blockGraph = [];

        return $this;
    }

    public function exec(string $cmdCommand)
    {
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = new ExecCommand($cmdCommand);
        } else {
            $this->blockGraph[$this->currBlock]->addToBody(new ExecCommand($cmdCommand));
            var_dump($this->currBlock);
            var_dump($this->blockGraph[$this->currBlock]);
        }
        var_dump($this->deepLevel);

        return $this;
    }

    public function if(string $cmdCommand, string $ifStatement)
    {
        if ($this->deepLevel == 0) {
            $newIf = new IfCommand($cmdCommand, $ifStatement);
            $this->chainCommands[] = $newIf;
        } else {
            $newIf = new IfCommand($cmdCommand, $ifStatement);
            $this->lastCommand->addToBody($newIf);
        }
        $this->deepLevel += 1;
        $this->operatorsGraph[$this->deepLevel] = $newIf;
        var_dump($this->deepLevel);

        return $this;
    }

    public function endIf()
    {
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel] != null ? $this->operatorsGraph[$this->deepLevel] : new NoneCommand();
        $this->deepLevel -= 1;
        var_dump($this->deepLevel);

        return $this;

    }

    public function then()
    {
        //        $this->lastOperator = $this->lastCommand;
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel];
        $this->lastCommand = new ThenCommand();
        $this->currBlock = $this->deepLevel.'.then';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        var_dump($this->deepLevel);

        return $this;

    }

    public function endThen()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->currBlock], 'then');
        var_dump($this->deepLevel);

        return $this;

    }

    public function else()
    {
        //        $this->lastOperator = $this->operatorsGraph[$this->deepLevel];
        $this->lastCommand = new ElseCommand();
        $this->currBlock = $this->deepLevel.'.else';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        var_dump($this->deepLevel);

        return $this;

    }

    public function endElse()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->currBlock], 'else');
        var_dump($this->deepLevel);

        return $this;

    }

    public function apply()
    {
        var_dump($this->chainCommands);
        var_dump("\n");
        var_dump($this->operatorsGraph);
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
