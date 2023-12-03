<?php

namespace Yaroslam\SSH2\Session\Connection;

class UserPasswordConnection implements ConnectionInterface
{
    public function connect($connection, array $connectProperties): bool
    {
        return ssh2_auth_password($connection, $connectProperties['user'], $connectProperties['password']);
    }
}
