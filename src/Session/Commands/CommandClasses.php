<?php

namespace Yaroslam\SSH2\Session\Commands;

enum CommandClasses
{
    /**
     * @var CommandClasses::Single одиночная команда
     */
    case Single;
    /**
     * @var CommandClasses::Block команда, которая принимает в свое тело другие команды
     */
    case Block;
    /**
     * @var CommandClasses::Operator команда, которая принимает в свое тело команды типа Block
     */
    case Operator;
    /**
     * @var CommandClasses::None команды, которые ничего не делают
     */
    case None;
}
