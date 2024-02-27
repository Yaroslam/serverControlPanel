<?php

use Yaroslam\SSH2\Session\Commands\CommandClasses;

return [
    'Single' => [CommandClasses::Single, CommandClasses::None, CommandClasses::Operator, CommandClasses::Block],
    'Operator' => [CommandClasses::Block],
    'Block' => [CommandClasses::Operator, CommandClasses::Single, CommandClasses::Block],
    'None' => [CommandClasses::Block, CommandClasses::Operator, CommandClasses::Single, CommandClasses::None],
];
