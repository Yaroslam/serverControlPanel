<?php

namespace Yaroslam\SSH2\Session\Commands\Exceptions;

use Exception;

class WorkflowTypeOrderException extends Exception
{
    public function __construct(array $commands)
    {
        parent::__construct();
        $this->message = 'Workflow order error, command with type '
            .$commands['prev']->name.
            ' cant stand before command with type '
            .$commands['next']->name;
    }
}
