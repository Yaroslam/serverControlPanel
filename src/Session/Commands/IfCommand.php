<?php

namespace Happy\ServerControlPanel\Session\Commands;

class IfCommand extends BaseCommand
{
    private array $body;

    private $ifOperator;

    private $ifStatment;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $ifStatment, $ifOperator)
    {
        $this->body = [];
        $this->commandText = $cmdText;
        $this->ifOperator = $cmdText;
        $this->ifStatment = $cmdText;
    }

    public function execution()
    {
        foreach ($this->body as $command) {
            yield from $command->execution();
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
