<?php

namespace Happy\ServerControlPanel\Session\Commands;

class ElseCommand extends BaseCommand
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
            $command->execute($shell);
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
