<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Traits\HasBody;
use Yaroslam\SSH2\Session\Commands\Traits\HasContext;

class SwitchCommand extends BaseCommand
{
    use HasBody;
    use HasContext;

    //    TODO
    //      1 перенсти сюда ексек команду
    //      2 добавить таймаут в констаркт (что бы задавать таймаут для эксек команды)
    //      3 добавлять в контекст результат ексек команды
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
}
