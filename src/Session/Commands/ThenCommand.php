<?php

namespace Yaroslam\ServerControlPanel\Session\Commands;

class ThenCommand extends BaseCommand
{
    private array $body;

    protected CommandClasses $commandType = CommandClasses::Block;

    public function __construct()
    {
        $this->body = [];
    }

    public function execution($shell)
    {
        foreach ($this->body as $command) {
            $command->execution($shell);
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
