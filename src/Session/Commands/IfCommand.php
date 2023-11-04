<?php

namespace Happy\ServerControlPanel\Session\Commands;

class IfCommand extends BaseCommand
{
    private array $body;

    private $ifStatment;

    private $ifResult;

    protected CommandClasses $commandType = CommandClasses::Operator;

    public function __construct(string $cmdText, $ifStatment)
    {
        $this->body = [];
        $this->commandText = $cmdText;
        $this->ifStatment = $ifStatment;
    }

    public function execution($shell)
    {
        fwrite($shell, $this->commandText.PHP_EOL);
        sleep(1);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }

        if (preg_match($this->ifStatment, $outLine)) {
            $this->ifResult = true;
            $this->body['then']->execution($shell);
        } else {
            $this->ifResult = false;
            $this->body['else']->execution($shell);
        }
    }

    public function addToBody(BaseCommand $command, $thenOrElse)
    {
        $this->body[$thenOrElse] = $command;
    }
}
