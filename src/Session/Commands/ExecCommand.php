<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeException;
use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeNorFoundException;

class ExecCommand extends BaseCommand
{
    private bool $needProof;

    protected CommandClasses $commandType = CommandClasses::Single;

    public function __construct(string $cmdText, bool $needProf = true)
    {
        $this->commandText = $cmdText;
        $this->needProof = $needProf;
    }

    // TODO
    //  fix regexp
    public function execution($shell)
    {

        $outArr = [];

        var_dump('exec '.$this->commandText);
        fwrite($shell, $this->commandText.';echo -en "\n$?"');
        sleep(4);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }

        if ($this->needProof) {
            if (! preg_match("/^(.*)\n(0|-?[1-9][0-9]*)$/s", $outLine, $matches)) {
                throw new ExitCodeNorFoundException("output didn't contain return status");
            } elseif ($matches[2] !== '0') {
                $outArr['command'] = $this->commandText;
                $outArr['exit_code'] = $matches[2];
                $outArr['output'] = $matches[1];
            } else {
                throw new ExitCodeException();
            }
        }

        return $outArr;
    }
}
