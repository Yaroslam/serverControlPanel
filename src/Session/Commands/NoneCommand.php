<?php

namespace Happy\ServerControlPanel\Session\Commands;

class NoneCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::None;

    public function execution($shell)
    {
        return null;
    }
}
