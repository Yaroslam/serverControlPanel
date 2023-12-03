<?php

namespace Yaroslam\SSH2\Session\Commands\Traits;

use Yaroslam\SSH2\Session\Commands\BaseCommand;

trait HasBody
{
    protected array $body = [];

    public function addToBody(BaseCommand $command)
    {
        $this->body[] = $command;
    }
}
