<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 * Класс case команды. Команда обязана быть в теле CommandCase. Выполняется, если соблюдается условие
 */
class CommandCase extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Block;
    /**
     * @var string Условие выполнения тела case команды
     */
    private string $caseStatement;

    /**
     * Конструктор команды
     * @param string $caseStatement Условие выполнения тела case команды
     */
    public function __construct(string $caseStatement) {
        $this->caseStatement = $caseStatement;
    }

    /**
     * Возвращает условие выполнения case команды
     * @api
     * @return string
     */
    public function getStatement(): string {
        return $this->caseStatement;
    }


    /**
     * Добавляет все команды тела во внутренний контекст выполнения
     * @api
     * @param $shell
     * @return array
     */
    public function execution($shell): array
    {
        foreach ($this->body as $command) {
            $this->addToContext($command->execution($shell));
        }

        return $this->getContext();
    }
}
