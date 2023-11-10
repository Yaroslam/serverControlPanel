<?php

namespace Yaroslam\ServerControlPanel\Session\Commands;

class NoneCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::None;

    public function execution($shell)
    {
        return null;
    }
}
