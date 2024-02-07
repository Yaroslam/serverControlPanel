<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 * Класс else команды, тело котрой выполняется, если не выполняется условие CommandIf
 */
class CommandElse extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Block;

    /**
     * Выполняет все команды в своем теле
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
