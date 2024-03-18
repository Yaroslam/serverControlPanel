<?php

namespace Yaroslam\SSH2\Session\Commands\Traits;

use Yaroslam\SSH2\Session\Commands\CommandBase;

/**
 * Трейт для команд, имеющих тело, в котором находятся другие команды
 */
trait HasBody
{
    /**
     * @var array тело для хранения команд
     */
    protected array $body = [];

    /**
     * Добавление команды в тело
     *
     * @api
     *
     * @param  CommandBase  $command команда, которая будет добавлена в тело
     */
    public function addToBody(CommandBase $command): void
    {
        $this->body[] = $command;
    }
}
