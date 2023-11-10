<?php

namespace Yaroslam\SSH2\Session\Connection;

class NoneConnection implements ConnectionInterface
{
    public function connect($connection, array $connectProperties)
    {
        ssh2_auth_password($connection, $connectProperties['user'], $connectProperties['password']);
    }
}
