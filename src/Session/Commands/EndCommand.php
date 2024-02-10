<?php

namespace Yaroslam\SSH2\Session\Commands;

/**
 * Трейт, используемый для команд, реализующих окончание команд
 * @todo Реализовать окончание команд по типам, что бы было соответствие Else -> endElse
 */
trait EndCommand
{
    /**
     * Возвращает тип команды
     * @return CommandClasses
     */
    public static function getCommandType()
    {
        return CommandClasses::None;
    }
}