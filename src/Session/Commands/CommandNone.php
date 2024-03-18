<?php

namespace Yaroslam\SSH2\Session\Commands;

/**
 * @todo пометить как @depricated или выпилить
 * Класс none команды, которая ничего не делает
 */
class CommandNone extends CommandBase
{
    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::None;

    /**
     * @return null
     */
    public function execution($shell)
    {
        return null;
    }
}
