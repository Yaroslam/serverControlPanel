<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 * Класс if команды, выполняет переданную команду, после чего исходя из вхождения или невхождения ifStatment в output
 * выполняет ветку then или else
 */
class CommandIf extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var string значение, вхождение которого в output будет проверено
     */
    private string $ifStatment;

    /**
     * @var bool Результат проверки
     */
    private bool $ifResult;

    /**
     * @var int таймаут перед выполнением команды
     */
    private int $timeout;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Operator;

    /**
     * Конструктор класса
     *
     * @param  string  $cmdText текст команды, которая будет выполнена
     * @param  string  $ifStatement значение, проверка на вхождение которого в output будет выполнена
     * @param  int  $timeout таймаут перед выполнением команды, по умолчанию равен 2 сек
     */
    public function __construct(string $cmdText, string $ifStatement, int $timeout = 2)
    {
        $this->commandText = $cmdText;
        $this->ifStatment = $ifStatement;
        $this->timeout = $timeout;
    }

    /**
     * Выполняет с помощью команды exec переданную команду.
     * После чего делает проверка на вхождение в output ifStatment.
     * Если значение найдено, то выполняет команды в ветке then.
     * Иначе выполняет команды в ветке else
     *
     * @api
     *
     * @return array контекст выполнения внутренних команд
     *
     * @throws Exceptions\ExitCodeException
     * @throws Exceptions\ExitCodeNotFoundException
     */
    public function execution($shell): array
    {
        $exec = new CommandExec($this->commandText, timeout: $this->timeout);
        $outLine = $exec->execution($shell)['output'];
        $this->addToContext($outLine);
        if (preg_match("/$this->ifStatment/", $outLine)) {
            $this->ifResult = true;
            $this->addToContext($this->body['then']->execution($shell));
        } else {
            $this->ifResult = false;
            $this->addToContext($this->body['else']->execution($shell));
        }

        return $this->getContext();
    }

    /**
     * Добавляет в тело ветку с переданной командой
     *
     * @todo параметризировать параметр thenOrElse
     *
     * @api
     *
     * @param  CommandBase  $command команда, которая будет добавлена в ветку $thenOrElse
     * @param  string  $thenOrElse наименование ветки else или then
     */
    public function addToBody(CommandBase $command, string $thenOrElse): void
    {
        $this->body[$thenOrElse] = $command;
    }
}
