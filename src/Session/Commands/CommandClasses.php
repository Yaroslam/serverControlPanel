<?php

namespace Happy\ServerControlPanel\Session\Commands;

enum CommandClasses
{
    case Single;
    case Block;
    case Operator;
    case None;
}
