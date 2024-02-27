<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 * Класс for команды, тело которой выполняется в for цикле, пока не будут выполнено условие цикла
 */
class CommandFor extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var int Старт отсчета for цикла
     */
    private int $forStart;

    /**
     * @var int шаг for цикла
     */
    private int $forStep;

    /**
     * @var int значение остановки for цикла
     */
    private int $forStop;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Block;

    /**
     * Конструктор класса
     *
     * @param  int  $forStart стартовое значение цикла
     * @param  int  $forStop значение окончания цикла
     * @param  int  $forStep шаг цикла, по умолчанию равен 1
     */
    public function __construct(int $forStart, int $forStop, int $forStep = 1)
    {
        $this->forStart = $forStart;
        $this->forStop = $forStop;
        $this->forStep = $forStep;
    }

    /**
     * Выполняет все команды в своем теле, пока $forStart < $forStop
     *
     * @api
     *
     * @return array контекст выполнения внутренних команд
     */
    public function execution($shell): array
    {

        for ($i = $this->forStart; $i < $this->forStop; $i += $this->forStep) {
            foreach ($this->body as $command) {
                $this->addToContext($command->execution($shell));
            }
        }

        return $this->getContext();
    }
}
