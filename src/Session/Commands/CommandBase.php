<?php

namespace Yaroslam\SSH2\Session\Commands;

/**
 * Базовый класс для всех команд
 */
abstract class CommandBase
{
    /**
     * @var string текст команды, который будет исполнен
     */
    protected string $commandText;

    /**
     * @var CommandClasses тип команды
     */
    protected CommandClasses $commandType;

    /**
     * Возвращает название команды
     *
     * @api
     *
     * @return array|string|string[]
     */
    public function getCommandName(): array|string
    {
        return str_replace('Command', '', $this::class);
    }

    /**
     * Возвращает класс команды
     *
     * @api
     */
    public function getCommandType(): CommandClasses
    {
        return $this->commandType;
    }

    /**
     * Исполняет команду в переданном $shell
     *
     * @api
     *
     * @param  resource  $shell ресурс консоли, в котрой будут выполнены команды
     */
    abstract public function execution($shell);
}
// типы команд: single->exec, block->then,else,while,case,for operator->if,switch, Non->заглушка
// блоки хранят массив других команд, операторы хранят условия выполнения команд и другие команды
// топ левел команды: if, exec, while, switch
// команды нижних уровней: then, else, case, exec
// зависимость оператор->тело if->then, if->else, while->all, switch->case
//  then->all, else->all, case->all, exec->none, for->all
// выполнение команд:
//  1)вызываем execution верхних команд, метод execution операторов вызывает execution вложенных команд
