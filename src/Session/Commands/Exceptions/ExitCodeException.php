<?php

namespace Yaroslam\SSH2\Session\Commands\Exceptions;

/**
 * Ошибка возникновения непредусмотренного exit code
 */
class ExitCodeException extends \Exception
{
    public function __construct(string $exitCode, string $command)
    {
        parent::__construct();
        $this->message = "$command raise exit code $exitCode";
    }


}
