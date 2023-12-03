<?php

namespace Yaroslam\SSH2\Session\Commands;

class ExecCommand extends BaseCommand
{
    protected CommandClasses $commandType = CommandClasses::Single;

    public function __construct(string $cmdText)
    {
        $this->commandText = $cmdText;
    }

    // TODO
    //  переделать на стримы эрроров и вывода или stream_get_mate_data
    public function execution($shell)
    {
        var_dump('exec '.$this->commandText);
        fwrite($shell, $this->commandText.PHP_EOL);
        sleep(1);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }

        return $outLine;
    }
}
