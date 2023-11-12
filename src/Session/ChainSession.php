<?php

namespace Yaroslam\SSH2\Session;

// TODO:
//  1)сделать иннер и реал действия
//  2)вывод ошибок
//  3)глобальный и локальный контекст выполнения
//  4)очистка контекста от введенных команд

use Yaroslam\SSH2\Session\Commands\BaseCommand;
use Yaroslam\SSH2\Session\Commands\ElseCommand;
use Yaroslam\SSH2\Session\Commands\EndElseCommand;
use Yaroslam\SSH2\Session\Commands\EndIfCommand;
use Yaroslam\SSH2\Session\Commands\EndThenCommand;
use Yaroslam\SSH2\Session\Commands\ExecCommand;
use Yaroslam\SSH2\Session\Commands\IfCommand;
use Yaroslam\SSH2\Session\Commands\ThenCommand;

class ChainSession extends AbstractSession
{
    private array $chainContext;

    private $shell;

    private array $chainCommands;

    private BaseCommand $lastCommand;

    private int $deepLevel;

    private array $operatorsGraph;

    private array $blockGraph;

    private string $currBlock;

    private array $workFlowTypes;

    public function initChain()
    {
        $this->shell = ssh2_shell($this->connector->getConnectionTunnel());
        $this->deepLevel = 0;
        $this->operatorsGraph = [];
        $this->blockGraph = [];
        $this->workFlowTypes = [];

        return $this;
    }

    public function exec(string $cmdCommand)
    {
        $execCommand = new ExecCommand($cmdCommand);
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = $execCommand;
        } else {
            $this->blockGraph[$this->currBlock]->addToBody($execCommand);
        }
        $this->workFlowTypes[] = $execCommand->getCommandType();

        return $this;
    }

    public function if(string $cmdCommand, string $ifStatement)
    {
        $newIf = new IfCommand($cmdCommand, $ifStatement);
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = $newIf;
        } else {
            $this->lastCommand->addToBody($newIf);
        }
        $this->deepLevel += 1;
        $this->operatorsGraph[$this->deepLevel] = $newIf;
        $this->workFlowTypes[] = $newIf->getCommandType();

        return $this;
    }

    public function endIf()
    {
        $this->deepLevel -= 1;
        $this->workFlowTypes[] = EndIfCommand::getCommandType();

        return $this;

    }

    public function then()
    {
        $this->lastCommand = new ThenCommand();
        $this->currBlock = $this->deepLevel.'.then';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        $this->workFlowTypes[] = $this->lastCommand->getCommandType();

        return $this;

    }

    public function endThen()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->deepLevel.'.then'], 'then');
        $this->workFlowTypes[] = EndThenCommand::getCommandType();

        return $this;

    }

    public function else()
    {
        $this->lastCommand = new ElseCommand();
        $this->currBlock = $this->deepLevel.'.else';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        $this->workFlowTypes[] = $this->lastCommand->getCommandType();

        return $this;

    }

    public function endElse()
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->deepLevel.'.else'], 'else');
        $this->workFlowTypes[] = EndElseCommand::getCommandType();

        return $this;

    }

    public function apply()
    {
        if ($this->checkWorkFlow()) {
            foreach ($this->chainCommands as $command) {
                $command->execution($this->shell);
            }
        }

        return $this;

    }

    public function getExecContext()
    {
        return $this->chainContext;
    }

    private function checkWorkFlow(): bool
    {
        $rules = require __DIR__.'/Commands/Rules/Rules.php';
        for ($i = 0; $i < count($this->workFlowTypes) - 1; $i++) {
            if (! in_array($this->workFlowTypes[$i + 1], $rules[$this->workFlowTypes[$i]->name()])) {
                throw new \Error();
            }
        }

        return true;
    }
}
