<?php

namespace Yaroslam\SSH2\Session\Commands;

use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeException;
use Yaroslam\SSH2\Session\Commands\Exceptions\ExitCodeNotFoundException;

/**
 * Класс exec команды, который выполняет переданную в него команду
 */
class CommandExec extends CommandBase
{
    /**
     * @var bool флаг проверки значения статус код по умолчанию равен true, если равен true - проверка проводится, если false - нет.
     */
    private bool $needProof;

    /**
     * @var int таймаут перед выполнением команды
     */
    private int $timeout;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType = CommandClasses::Single;

    /**
     * Конструктор класса
     *
     * @param  string  $cmdText текст команды
     * @param  bool  $needProf флаг проверки значения статус код по умолчанию равен true, если равен true - проверка проводится, если false - нет.
     * @param  int  $timeout задержка перед выполнением команды, по умолчанию равна 4 секунды.
     */
    public function __construct(string $cmdText, bool $needProf = true, int $timeout = 4)
    {
        $this->commandText = $cmdText;
        $this->needProof = $needProf;
        $this->timeout = $timeout;
    }

    /**
     * Выполняет commandText в переданном shell
     *
     * @return array массив результатов работы команды формата
     * ['command' => commandText, 'exit_code' => код выполнения, output => вывод команды]
     *
     * @throws ExitCodeException
     * @throws ExitCodeNotFoundException
     */
    public function execution($shell): array
    {
        $outArr = [];
        fwrite($shell, $this->commandText.';echo -e "\n$?"'.PHP_EOL);
        sleep($this->timeout);
        $outLine = '';
        while ($out = fgets($shell)) {
            $outLine .= $out."\n";
        }
        if ($this->needProof) {
            if (! preg_match("#^(.*)\n(0|-?[1-9][0-9]*).*\n#s", $outLine, $matches)) {
                throw new ExitCodeNotFoundException($this->commandText);
            } elseif ($matches[2] === '0') {
                $outArr['command'] = $this->commandText;
                $outArr['exit_code'] = $matches[2];
                $outArr['output'] = str_replace($this->commandText.';echo -e "\n$?"'.PHP_EOL, '', $matches[1]);
            } else {
                throw new ExitCodeException($matches[2], $this->commandText);
            }
        }

        return $outArr;
    }
}
