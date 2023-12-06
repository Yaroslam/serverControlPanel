<?php

namespace Yaroslam\SSH2\Session\Commands\Exceptions;

class ExitCodeException extends \Exception
{
    public function __construct(string $exitCode, string $command)
    {
        parent::__construct();
        $this->message = "$command raise exit code $exitCode";
    }


}
