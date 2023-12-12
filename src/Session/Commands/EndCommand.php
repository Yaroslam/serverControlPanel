<?php

namespace Yaroslam\SSH2\Session\Commands;

trait EndCommand
{
    public static function getCommandType()
    {
        return CommandClasses::None;
    }
}