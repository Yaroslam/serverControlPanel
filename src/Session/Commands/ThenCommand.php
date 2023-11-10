<?php

namespace Happy\ServerControlPanel\Session\Commands;

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
            var_dump(1212323423424);
            $command->execution($shell);
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
        var_dump($this->body);
    }
}
