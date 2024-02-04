<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

class CommandSwitch extends CommandBase
{
    use HasBody;
    use HasContext;

    private bool $breakable;

    private $timeout;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $breakable = true, int $timeout = 2)
    {
        $this->commandText = $cmdText;
        $this->timeout = $timeout;
        $this->breakable = $breakable;
    }

    public function execution($shell): array
    {
        $exec = new CommandExec($this->commandText, timeout: $this->timeout);
        $outLine = $exec->execution($shell)['output'];
        $this->addToContext($outLine);
        foreach ($this->body as $case) {
            if (preg_match("/$case->getStatment/", $outLine)) {
                $this->addToBody($case->execution($shell));
                if ($this->breakable) {
                    break;
                }
            }
        }

        return $this->getContext();
    }
}
