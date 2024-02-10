<?php

namespace Yaroslam\SSH2\Session;

use Yaroslam\SSH2\Session\Commands\CommandBase;
use Yaroslam\SSH2\Session\Commands\CommandCase;
use Yaroslam\SSH2\Session\Commands\CommandElse;
use Yaroslam\SSH2\Session\Commands\CommandExec;
use Yaroslam\SSH2\Session\Commands\CommandFor;
use Yaroslam\SSH2\Session\Commands\CommandIf;
use Yaroslam\SSH2\Session\Commands\CommandThen;
use Yaroslam\SSH2\Session\Commands\EndElseCommand;
use Yaroslam\SSH2\Session\Commands\EndForCommand;
use Yaroslam\SSH2\Session\Commands\EndIfCommand;
use Yaroslam\SSH2\Session\Commands\EndThenCommand;
use Yaroslam\SSH2\Session\Commands\Exceptions\WorkflowTypeOrderException;

/**
 * Класс сессии, которая сохраняет состояние между вызовами и может пользоваться всеми командами
 * @todo добавить case switch
 */
class ChainSession extends AbstractSession
{
    /**
     * @var array глобальный контекст выполнения
     */
    private array $chainContext;

    /**
     * @var resource ssh2 ресурс
     */
    private $shell;

    /**
     * @var array массив, хранящий все записанные в цепочку команды
     */
    private array $chainCommands;

    /**
     * @var CommandThen|CommandElse|CommandCase|CommandFor последняя добавленная команда
     * @todo подумать над переименованием в ластБлок
     */
    private CommandThen|CommandElse|CommandCase|CommandFor $lastCommand;

    /**
     * @var int текущий уровень глубины в месте добавления команд
     */
    private int $deepLevel;

    /**
     * @var array массив, хранящий глобальный список операторов, согласно их глубине
     */
    private array $operatorsGraph;

    /**
     * @var array массив, хранящий список блоков, согласно их глубине
     */
    private array $blockGraph;

    /**
     * @var string глобальное наименование текущего блока
     */
    private string $currBlock;

    /**
     * @var array глобальный список типов команд, согласно порядку их выполнения
     */
    private array $workFlowTypes;

    /**
     * @var array массив сохраненных функций
     */
    private array $functions;

    /**
     * @param $withFakeStart
     * @return $this
     */
    public function initChain(bool $withFakeStart = true): ChainSession
    {
        $this->shell = ssh2_shell($this->connector->getSsh2Connect());
        $withFakeStart ? $this->fakeStart() :
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

    /**
     * @return void
     */
    private function fakeStart(): void
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

    /**
     * @param string $cmdCommand
     * @param bool $needProof
     * @param int $timeout
     * @return $this
     */
    public function exec(string $cmdCommand, bool $needProof = true, int $timeout = 4): ChainSession
    {
        $execCommand = new CommandExec($cmdCommand, $needProof, $timeout);
        if ($this->deepLevel == 0) {
            $this->chainCommands[] = $execCommand;
        } else {
            $this->blockGraph[$this->currBlock]->addToBody($execCommand);
        }
        $this->workFlowTypes[] = $execCommand->getCommandType();

        return $this;
    }

    /**
     * @param string $cmdCommand
     * @param string $ifStatement
     * @return $this
     */
    public function if(string $cmdCommand, string $ifStatement): ChainSession
    {
        $newIf = new CommandIf($cmdCommand, $ifStatement);
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

    /**
     * @return $this
     */
    public function endIf(): ChainSession
    {
        $this->deepLevel -= 1;
        $this->workFlowTypes[] = EndIfCommand::getCommandType();

        return $this;

    }

    /**
     * @return $this
     */
    public function then(): ChainSession
    {
        $this->lastCommand = new CommandThen();
        $this->currBlock = $this->deepLevel.'.then';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        $this->workFlowTypes[] = $this->lastCommand->getCommandType();

        return $this;

    }

    /**
     * @return $this
     */
    public function endThen(): ChainSession
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->deepLevel.'.then'], 'then');
        $this->workFlowTypes[] = EndThenCommand::getCommandType();

        return $this;

    }

    /**
     * @return $this
     */
    public function else(): ChainSession
    {
        $this->lastCommand = new CommandElse();
        $this->currBlock = $this->deepLevel.'.else';
        $this->blockGraph[$this->currBlock] = $this->lastCommand;
        $this->deepLevel += 1;
        $this->workFlowTypes[] = $this->lastCommand->getCommandType();

        return $this;

    }

    /**
     * @return $this
     */
    public function endElse(): ChainSession
    {
        $this->deepLevel -= 1;
        $this->operatorsGraph[$this->deepLevel]->addToBody($this->blockGraph[$this->deepLevel.'.else'], 'else');
        $this->workFlowTypes[] = EndElseCommand::getCommandType();

        return $this;

    }

    /**
     * @param $start
     * @param $stop
     * @param $step
     * @return $this
     */
    public function for($start, $stop, $step = 1): ChainSession
    {
        $newFor = new CommandFor($start, $stop, $step);
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

    /**
     * @return $this
     */
    public function endFor(): ChainSession
    {
        $this->deepLevel -= 1;
        $this->workFlowTypes[] = EndForCommand::getCommandType();

        return $this;
    }

    /**
     * @param $con
     * @param $output
     * @return array|array[]
     */
    public function getExecContext($con = [], array $output = ['command' => [], 'exit_code' => [], 'output' => []]): array
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

    /**
     * @param array $workflow
     * @return bool|WorkflowTypeOrderException
     * @throws WorkflowTypeOrderException
     */
    private function checkWorkFlow(array $workflow): bool|WorkflowTypeOrderException
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

    /**
     * @param string $name
     * @return $this
     */
    public function declareFunction(string $name): ChainSession
    {
        $this->functions[$name] = [];

        return $this;
    }

    /**
     * @param string $name
     * @return void
     */
    public function endFunction(string $name): void
    {
        $this->functions[$name] = ['chain' => $this->chainCommands,
            'workflow' => $this->workFlowTypes,
        ];
    }

    /**
     * @param string $name
     * @return $this
     * @throws WorkflowTypeOrderException
     */
    public function useFunction(string $name): ChainSession
    {
        if ($this->checkWorkFlow($this->functions[$name]['workflow'])) {
            foreach ($this->functions[$name]['chain'] as $command) {
                $command->execution($this->shell);
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws WorkflowTypeOrderException
     */
    public function apply(): ChainSession
    {
        if ($this->checkWorkFlow($this->workFlowTypes)) {
            foreach ($this->chainCommands as $command) {
                $this->chainContext[] = $command->execution($this->shell);
            }
        }

        return $this;
    }
}
