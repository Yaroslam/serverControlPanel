<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

class ThenCommand extends BaseCommand
{
    use HasBody;
    use HasContext;

    protected CommandClasses $commandType = CommandClasses::Block;

    public function execution($shell)
    {
        foreach ($this->body as $command) {
            $this->addToContext($command->execution($shell));
        }

        return $this->getContext();
    }
}
