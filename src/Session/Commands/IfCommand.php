<?php

namespace Yaroslam\ServerControlPanel\Session\Commands;

class IfCommand extends BaseCommand
{
    private array $body;

    private $ifStatment;

    private $ifResult;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $ifStatement)
    {
        $this->body = [];
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
        var_dump($outLine);
        if (preg_match("/$this->ifStatment/", $outLine)) {
            $this->ifResult = true;
            $this->body['then']->execution($shell);
            var_dump('true '.$this->commandText.' '.$this->ifStatment);
        } else {
            $this->ifResult = false;
            var_dump('false '.$this->commandText.' '.$this->ifStatment);
            $this->body['else']->execution($shell);
        }
    }

    public function addToBody(BaseCommand $command, $thenOrElse)
    {
        $this->body[$thenOrElse] = $command;
    }
}
