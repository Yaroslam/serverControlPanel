<?php

namespace Yaroslam\SSH2\Session\Commands;

abstract class CommandBase
{
    protected string $commandText;

    protected CommandClasses $commandType;

    public function getCommandName()
    {
        return str_replace('Command', '', $this::class);
    }

    public function getCommandType()
    {
        return $this->commandType;
    }

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
