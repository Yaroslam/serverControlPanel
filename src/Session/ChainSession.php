<?php

namespace Yaroslam\SSH2\Session;

use Yaroslam\SSH2\Session\Commands\ElseCommand;
use Yaroslam\SSH2\Session\Commands\EndElseCommand;
use Yaroslam\SSH2\Session\Commands\EndForCommand;
use Yaroslam\SSH2\Session\Commands\EndIfCommand;
use Yaroslam\SSH2\Session\Commands\EndThenCommand;
use Yaroslam\SSH2\Session\Commands\Exceptions\WorkflowTypeOrderException;
use Yaroslam\SSH2\Session\Commands\ExecCommand;
use Yaroslam\SSH2\Session\Commands\ForCommand;
use Yaroslam\SSH2\Session\Commands\IfCommand;
use Yaroslam\SSH2\Session\Commands\ThenCommand;

class ChainSession extends AbstractSession
{
    private array $chainContext;

    private $shell;

    private array $chainCommands;

    private $lastCommand;

    private int $deepLevel;

    private array $operatorsGraph;

    private array $blockGraph;

    private string $currBlock;

    private array $workFlowTypes;

    private array $functions;

    public function initChain()
    {
        $this->shell = ssh2_shell($this->connector->getSsh2Connect());
        $this->fakeStart();
        $this->deepLevel = 0;
        $this->operatorsGraph = [];
        $this->blockGraph = [];
        $this->workFlowTypes = [];
        $this->functions = [];
        $this->chainContext = [];
        $this->chainCommands = [];

        return $this;
    }

    //    fake start для того, что бы первая выполняемая команда не выводилась вместе с сообщениями старта системы
    private function fakeStart()
    {
        $this->deepLevel = 0;
        $this->operatorsGraph = [];
        $this->blockGraph = [];
        $this->workFlowTypes = [];
        $this->functions = [];
        $this->chainContext = [];
        $this->chainCommands = [];
        $this->exec('echo start', false)->apply();
    }

    public function exec(string $cmdCommand, bool $needProof = true, int $timeout = 4)
    {
        $execCommand = new ExecCommand($cmdCommand, $needProof, $timeout);
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

    public function for($start, $stop, $step = 1)
    {
        $newFor = new ForCommand($start, $stop, $step);
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = $newFor;
        } else {
            $this->lastCommand->addToBody($newFor);
        }
        $this->lastCommand = $newFor;
        $this->currBlock = $this->deepLevel.'.for';
        $this->deepLevel += 1;
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->workFlowTypes[] = $newFor->getCommandType();

        return $this;
    }

    public function endFor()
    {
        $this->deepLevel -= 1;
        $this->workFlowTypes[] = EndForCommand::getCommandType();

        return $this;
    }

    public function getExecContext($con = [], $output = ['command' => [], 'exit_code' => [], 'output' => []])
    {
        if ($con == []) {
            $con = $this->chainContext;
        }
        foreach ($con as $context) {
            if (array_key_exists('command', $con)) {
                $output['command'][] = $con['command'];
                $output['exit_code'][] = $con['exit_code'];
                $output['output'][] = $con['output'];

                return $output;
            } else {
                $output = $this->getExecContext($context, $output);
            }
        }

        return $output;
    }

    private function checkWorkFlow(array $workflow): bool
    {
        $rules = require __DIR__.'/Commands/Rules/Rules.php';
        for ($i = 0; $i < count($workflow) - 1; $i++) {
            if (! in_array($workflow[$i + 1], $rules[$workflow[$i]->name])) {
                throw new WorkflowTypeOrderException([
                    'prev' => $workflow[$i],
                    'next' => $workflow[$i + 1]]);
            }
        }

        return true;
    }

    public function declareFunction(string $name)
    {
        $this->functions[$name] = [];

        return $this;
    }

    public function endFunction(string $name)
    {
        $this->functions[$name] = ['chain' => $this->chainCommands,
            'workflow' => $this->workFlowTypes,
        ];
    }

    public function useFunction(string $name)
    {
        if ($this->checkWorkFlow($this->functions[$name]['workflow'])) {
            foreach ($this->functions[$name]['chain'] as $command) {
                $command->execution($this->shell);
            }
        }

        return $this;
    }

    public function apply()
    {
        var_dump('----------------apply-----------------');
        if ($this->checkWorkFlow($this->workFlowTypes)) {
            foreach ($this->chainCommands as $command) {
                $this->chainContext[] = $command->execution($this->shell);
            }
        }

        return $this;
    }
}
