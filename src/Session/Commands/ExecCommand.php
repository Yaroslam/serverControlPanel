<?php

namespace Yaroslam\ServerControlPanel\Session\Commands;

class ExecCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::Single;

    public function __construct(string $cmdText)
    {
        $this->commandText = $cmdText;
    }

    public function execution($shell)
    {
        var_dump("exec ".$this->commandText);
        fwrite($shell, $this->commandText.PHP_EOL);
        sleep(1);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }
        var_dump($outLine);
        //        return $outLine;
    }
}
