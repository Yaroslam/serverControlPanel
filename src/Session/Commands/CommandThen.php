<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

/**
 * Класс else команды, тело котрой выполняется, если выполняется условие CommandIf
 */
class CommandThen extends CommandBase
{
    use HasBody;
    use HasContext;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Block;

    /**
     * Выполняет все команды в своем теле
     *
     * @api
     *
     * @return array
     */
    public function execution($shell)
    {
        foreach ($this->body as $command) {
            $this->addToContext($command->execution($shell));
        }

        return $this->getContext();
    }
}
