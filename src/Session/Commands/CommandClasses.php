<?php

namespace Yaroslam\SSH2\Session\Commands;

enum CommandClasses
{
    case Single;
    case Block;
    case Operator;
    case None;
}
