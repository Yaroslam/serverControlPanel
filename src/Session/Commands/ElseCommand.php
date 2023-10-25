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

    public function execution()
    {
        foreach ($this->body as $command) {
            $execRes = $command->execution();
            if (gettype($execRes) == 'string') {
                yield $execRes;
            } else {
                yield from $execRes;
            }
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
