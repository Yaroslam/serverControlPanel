<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

class ForCommand extends BaseCommand
{
    use HasBody;
    use HasContext;

    private $forStart;

    private $forStep;

    private $forStop;

    protected CommandClasses $commandType = CommandClasses::Block;

    public function __construct(int $forStart, int $forStop, int $forStep = 1)
    {
        $this->forStart = $forStart;
        $this->forStop = $forStop;
        $this->forStep = $forStep;

    }

    public function execution($shell)
    {

        for ($i = $this->forStart; $i < $this->forStop; $i += $this->forStep) {
            foreach ($this->body as $command) {
                $this->addToContext($command->execution($shell));
            }
        }

        return $this->getContext();
    }
}
