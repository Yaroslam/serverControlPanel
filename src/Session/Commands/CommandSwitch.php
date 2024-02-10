<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 *  Класс case команды, которая выполняет переданною в нее команду, после чего проверяет внутренние case команды на соответствие
 *  */
class CommandSwitch extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var bool флаг прекращения проверки внутренних case. Если равен true, то проверка прекращается. Если равен False проверка не прекращается. По умолчанию равен True
     */
    private bool $breakable;

    /**
     * @var int Время задержки перед выполннением команды, по умолчанию равен 2 сек.
     */
    private int $timeout;

    /**
     * @var CommandClasses класс команды
     */
    protected CommandClasses $commandType = CommandClasses::Operator;

    /**
     * Конструктор класса
     *
     * @param  string  $cmdText текст выполняемой команды
     * @param  bool  $breakable флаг прекращения проверки внутренних case. Если равен true, то проверка прекращается. Если равен False проверка не прекращается. По умолчанию равен True
     * @param  int  $timeout Время задержки перед выполннением команды, по умолчанию равен 2 сек.
     */
    public function __construct(string $cmdText, bool $breakable = true, int $timeout = 2)
    {
        $this->commandText = $cmdText;
        $this->timeout = $timeout;
        $this->breakable = $breakable;
    }

    /**
     * Выполеняет переданную команду, после ее выполнения проверяет все внутренние case на соответствие их условиям
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
        foreach ($this->body as $case) {
            /* @var $case CommandCase */
            $caseStatement = $case->getStatement();
            if (preg_match("/$caseStatement/", $outLine)) {
                $this->addToBody($case->execution($shell));
                if ($this->breakable) {
                    break;
                }
            }
        }

        return $this->getContext();
    }
}
