<?php

namespace Yaroslam\SSH2\Session\Commands;

class NoneCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::None;

    public function execution($shell)
    {
        return null;
    }
}
