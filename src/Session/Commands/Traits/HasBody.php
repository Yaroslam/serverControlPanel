<?php

namespace Yaroslam\SSH2\Session\Commands\Traits;

use Yaroslam\SSH2\Session\Commands\CommandBase;

trait HasBody
{
    protected array $body = [];

    public function addToBody(CommandBase $command)
    {
        $this->body[] = $command;
    }
}
