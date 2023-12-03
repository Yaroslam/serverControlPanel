<?php

use Yaroslam\SSH2\Session\Commands\CommandClasses;

return [
    'Single' => [CommandClasses::Single, CommandClasses::None, CommandClasses::Operator],
    'Operator' => [CommandClasses::Block],
    'Block' => [CommandClasses::Operator, CommandClasses::Single],
    'None' => [CommandClasses::Block, CommandClasses::Operator, CommandClasses::Single, CommandClasses::None],
];
