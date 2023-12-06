<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeException;
use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeNotFoundException;

class ExecCommand extends BaseCommand
{
    private bool $needProof;
    private int $timeout;

    protected CommandClasses $commandType = CommandClasses::Single;

    public function __construct(string $cmdText, bool $needProf = true, $timeout=4)
    {
        $this->commandText = $cmdText;
        $this->needProof = $needProf;
        $this->timeout = $timeout;
    }

    // TODO
    //  fix regexp
    public function execution($shell)
    {

        $outArr = [];

        var_dump('exec '.$this->commandText);
        fwrite($shell, $this->commandText.';echo -e "\n$?"'.PHP_EOL);
        sleep($this->timeout);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }
        if ($this->needProof) {
            if (! preg_match("#^(.*)\n(0|-?[1-9][0-9]*).*\n#s", $outLine, $matches)) {
                throw new ExitCodeNotFoundException($this->commandText);
            } else if ($matches[2] === "0") {
                $outArr['command'] = $this->commandText;
                $outArr['exit_code'] = $matches[2];
                $outArr['output'] = $matches[1];
            } else {
                throw new ExitCodeException($matches[2], $this->commandText);
            }
        }

        return $outArr;
    }
}
