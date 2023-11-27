<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;

class ElseCommand extends BaseCommand
{
    use HasBody;

    protected CommandClasses $commandType = CommandClasses::Block;

    public function execution($shell)
    {
        foreach ($this->body as $command) {
            $command->execution($shell);
        }
    }
}
