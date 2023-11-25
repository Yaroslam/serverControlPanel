<?php

namespace Yaroslam\SSH2\Session\Commands;

class ForCommand extends BaseCommand
{
    private array $body;

    private $forStart;

    private $forStep;

    private $forStop;

    protected CommandClasses $commandType = CommandClasses::Block;

    public function __construct(int $forStart, int $forStop, int $forStep = 1)
    {
        $this->body = [];
        $this->forStart = $forStart;
        $this->forStop = $forStop;
        $this->forStep = $forStep;

    }

    public function execution($shell)
    {

        for ($i = $this->forStart; $i < $this->forStop; $i += $this->forStep) {
            foreach ($this->body as $command) {
                $command->execution($shell);
            }
        }
    }

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
