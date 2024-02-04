<?php

namespace Yaroslam\SSH2\Session\Commands;

class CommandNone extends CommandBase
{
    protected CommandClasses $commandType = CommandClasses::None;

    public function execution($shell)
    {
        return null;
    }
}
