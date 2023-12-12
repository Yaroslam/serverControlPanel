<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

class IfCommand extends BaseCommand
{
    //    TODO
    //      1 перенсти сюда ексек команду
    //      2 добавить таймаут в констаркт (что бы задавать таймаут для эксек команды)
    //      3 добавлять в контекст результат ексек команды
    use HasBody;
    use HasContext;

    private $ifStatment;

    private $ifResult;

    private $timeout;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $ifStatement, int $timeout = 2)
    {
        $this->commandText = $cmdText;
        $this->ifStatment = $ifStatement;
        $this->timeout = $timeout;
    }

    public function execution($shell)
    {
        $exec = new ExecCommand($this->commandText, timeout: $this->timeout);
        $outLine = $exec->execution($shell)['output'];
        $this->addToContext($outLine);
        if (preg_match("/$this->ifStatment/", $outLine)) {
            $this->ifResult = true;
            $this->addToContext($this->body['then']->execution($shell));
        } else {
            $this->ifResult = false;
            $this->addToContext($this->body['else']->execution($shell));
        }

        return $this->getContext();
    }

    public function addToBody(BaseCommand $command, $thenOrElse)
    {
        $this->body[$thenOrElse] = $command;

    }
}
