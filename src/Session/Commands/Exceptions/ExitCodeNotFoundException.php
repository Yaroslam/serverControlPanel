<?php

namespace Yaroslam\SSH2\Session\Commands\Exceptions;

/**
 * Ошибка не нахождения  exit code
 */
class ExitCodeNotFoundException extends \Exception
{
    public function __construct(string $command)
    {
        parent::__construct();
        $this->message = "output didn't contain return status command => $command";
    }
}
