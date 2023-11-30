<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

//TODO
// очистка вывода от информации, которая может помешать определению(текущая папка, сама команда)
class IfCommand extends BaseCommand
{
    use HasBody;
    use HasContext;

    private $ifStatment;

    private $ifResult;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $ifStatement)
    {
        $this->commandText = $cmdText;
        $this->ifStatment = $ifStatement;
    }

    public function execution($shell)
    {
        fwrite($shell, $this->commandText.PHP_EOL);
        sleep(1);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }
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
