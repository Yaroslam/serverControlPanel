<?php

namespace Happy\ServerControlPanel\Session\Commands;

class ExecCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::Single;

    public function __construct(string $cmdText)
    {
        $this->commandText = $cmdText;
    }

    public function execution()
    {
        return $this->commandText;
    }
}
